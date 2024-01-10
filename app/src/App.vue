<template>
    <div class="iktickets-app">
        <router-view></router-view>
    </div>
</template>

<script setup>

let vh = window.innerHeight * 0.01;
document.documentElement.style.setProperty("--vh", `${vh}px`);

// set on resize
window.addEventListener("resize", () => {
    let vh = window.innerHeight * 0.01;
    document.documentElement.style.setProperty("--vh", `${vh}px`);
});
</script>

<style lang="scss">
@import "./assets/scss/app.scss";

:root {
    --vh: #{vh};

    @each $name, $value in $theme {
        --#{ $name }: #{$value};
    }
}
</style>
