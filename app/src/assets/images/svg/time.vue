<template>
    <svg :width="size" :height="size" fill="currentColor" version="1.1" xmlns="http://www.w3.org/2000/svg" viewBox="54 54 1092 1092">  <path d="m600 54c-301.07 0-546 244.93-546 546s244.93 546 546 546 546-244.93 546-546-244.93-546-546-546zm0 1008c-254.75 0-462-207.25-462-462s207.25-462 462-462 462 207.25 462 462-207.25 462-462 462zm42-835.99h-84v391.87l218.89 210.41 58.223-60.562-193.11-185.6z"></path> </svg>

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