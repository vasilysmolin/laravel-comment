<template>
    <div class="row-cols-md-4">
        <label class="form-label">Отзывы</label>
        <template v-if="user">
        <form v-on:submit.prevent="saveComment('form0')" :ref="`form0`">
            <textarea name="text" id="text" cols="30" rows="10"></textarea>
            <input type="hidden" name="comment" value="">
            <button type="submit">Отправить</button>
        </form>
        </template>
        <div v-for="(review, index) in comments" :key="index">
            <p>{{review.user.name}} {{dateFormat(review.created_at)}}</p>
            <p>{{review.text}}</p>
            <template v-if="user">
            <form v-on:submit.prevent="saveComment(`form${review.id}`)" :ref="`form${review.id}`">
                <textarea name="text" :id="`text-${review.id}`" cols="30" rows="10"></textarea>
                <input type="hidden" name="comment" :value="review.id">
                <button type="submit">Отправить</button>
            </form>
            </template>
            <div v-for="(comment, index) in review.children_comments" :key="index">
                <p>{{review.user.name}} {{dateFormat(review.created_at)}}</p>
                <p>{{comment.text}}</p>
            </div>
        </div>
    </div>
</template>

<script>

export default {
    name: 'Reviews',
    props: {
        reviews: {
            required: false,
            type: Object,
            default: [],
        },
        user: {
            required: false,
            type: Object,
            default: {},
        },
        article: {
            required: true,
            type: Number,
            default: null,
        },
    },
    data() {
        return {
            text: null,
            comments: []
        }
    },
    mounted() {
        this.comments = this.reviews;
    },
    methods: {
        dateFormat(date) {
            if (date) {
                const dates = new Date(date);
                const formatter = new Intl.DateTimeFormat('ru-RU', {
                    day: 'numeric',
                    month: 'long',
                    year: 'numeric',
                });
                return formatter.format(dates);
            }
            return null;
        },

        saveComment(formID) {
            let data = new FormData();
            if (_.isArray(this.$refs[formID])) {
                data.append('comment_id', this.$refs[formID][0].comment.value);
                data.append('text', this.$refs[formID][0].text.value);
            } else {
                data.append('comment_id', this.$refs[formID].comment.value);
                data.append('text', this.$refs[formID].text.value);
            }
            data.append('user_id', this.user.id);
            data.append('article_id', this.article);
            axios.post(`api/comments`, data).then((res) => {
                if (res.status === 201) {
                    axios.get(`api/comments?article_id=${this.article}`).then((res) => {
                        this.comments = res.data.comments;
                        this.$refs.form.text.value = '';
                    }).catch((error) => {
                        console.log(error.response.data.errors);
                    });
                }
            }).catch((error) => {
                console.log(error.response.data.errors);
            });
        }

    },
}
</script>
