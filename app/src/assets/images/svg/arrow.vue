<template>
    <svg :width="size" :height="size" fill="currentColor" version="1.1"  xmlns="http://www.w3.org/2000/svg"
        viewBox="299.98 359.97 626 469.47">
        <path d="m657.3 795.48 33.934 33.934 234.73-234.72-234.73-234.72-33.934 33.934 176.79 176.79h-534.09v48h534.09z">
        </path>
    </svg>
</template>

<script>
export default {
    props: {
        size: {
            type: Number,
            default: 20,
        },
    },
};

</script>