<template>
    <div class="row-cols-md-4">
        <label class="form-label">Отзывы</label>
        <div v-for="(review, index) in reviews" :key="index">
            <p>{{review.user.name}} {{dateFormat(review.created_at)}}</p>
            <p>{{review.text}}</p>
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
    },
    data() {
        return {
        }
    },
    mounted() {

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
        }
    },
}
</script>
