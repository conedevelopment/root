<template>
    <header class="app-header">
        <div class="app-header__heading">
            <h1 class="app-header__title">Posts</h1>
            <nav class="app-header__breadcrumbs" aria-label="Breadcrumbs">
                <ol class="breadcrumbs">
                    <li class="breadcrumb-item">
                        <a href="#">Dashboard</a>
                    </li>
                    <li class="breadcrumb-item">
                        <span aria-current="page">Posts</span>
                    </li>
                </ol>
            </nav>
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
    import { Link } from '@inertiajs/inertia-vue3';

    export default {
        components: {
            Link,
        },
    }
</script>
