<template>
    <div class="iktickets-billetterie-page">
        <h1>hello world</h1>
    </div>
</template>
<script setup>
import { useWpdataStore } from '../../stores/wpdata';

const wpdataStore = useWpdataStore();

</script>

<style lang="scss" scoped>
.iktickets-billetterie-page {
    .flex-grid {
        display: flex;
        flex-wrap: wrap;
        gap: var(--iktickets-sp-xl);
        margin-bottom: var(--iktickets-sp-lg);

        >* {
            flex: 1 1 10%;
        }

        @media screen and (max-width: 768px) {
            flex-direction: column;
        }
    }

    .subtitle {
        margin: var(--iktickets-sp-mdl) 0 var(--iktickets-sp-sm);
    }

    .chapeau {
        display: flex;
        height: 100%;
        align-items: center;
        font-size: var(--iktickets-font-size-xl);
        font-weight: var(--font-weight-bold);
        border-top: 1px solid var(--iktickets-color-grey-light);
        border-bottom: 1px solid var(--iktickets-color-grey-light);
        padding: var(--iktickets-sp-md) 0;
    }

    .complementary-info {
        color: var(--iktickets-color-main);
        background-color: var(--iktickets-color-grey-lighter);
        padding: var(--sp-iktickets-larger);

        .title {
            margin-top: 0;
            margin-bottom: var(--iktickets-sp-md);
            font-size: var(--iktickets-font-size-lg);
            font-family: var(--font-iktickets-medium);
            font-weight: var(--font-weight-iktickets-medium);
        }

        .content {
            font-size: var(--iktickets-font-size-md);
        }
    }

    .event-type {
        display: flex;
        align-items: center;
        background-color: var(--iktickets-color-grey-lighter);
        padding: var(--iktickets-sp-md);

        svg {
            color: var(--iktickets-color-main);
        }

        h3 {
            margin: 0;
            margin-left: var(--iktickets-sp-sm);
            font-size: var(--iktickets-font-size-lg);
            font-family: var(--font-iktickets-medium);
            font-weight: var(--font-weight-iktickets-medium);
            text-transform: initial;
        }
    }
}
</style>