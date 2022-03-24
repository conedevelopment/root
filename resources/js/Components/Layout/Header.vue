<template>
    <header class="app-header">
        <div class="app-header__heading">
            <h1 class="app-header__title">{{ title }}</h1>
        </div>
        <div class="app-header__actions">
            <button type="button" class="btn btn--primary btn--open-search">
                <Icon class="btn__icon btn__icon--start" name="magnifier"></Icon>
                Quick search...<span class="btn__keybind">Ctrl K</span>
            </button>
        </div>
    </header>
</template>

<script>
    export default {
        props: {
            title: {
                type: String,
                required: true,
            },
        },
    }
</script>
