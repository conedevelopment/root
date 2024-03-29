<div id="notifications" class="app-notification" x-data="notifications('{{ $url }}')" data-turbo-permanent>
    <button
        class="btn btn--light btn--icon btn--counter app-notification__toggle"
        aria-label="{{ __('Notifications') }}"
        x-bind:aria-expanded="open"
        x-on:click="open = ! open"
    >
        <x-root::icon name="notifications" class="btn__icon" />
        <template x-if="unread > 0">
            <span class="btn__counter" x-text="unread"></span>
        </template>
    </button>
    <template x-teleport="#modals">
        <div>
            <div class="app-notification__backdrop" x-on:click="open = false" x-transition x-show="open"></div>
            <div class="app-notification__drawer" x-bind:data-state="open ? 'open' : 'closed'" x-transition>
                <div class="app-notification__header">
                    <h2 class="app-notification__title">{{ __('Notifications') }}</h2>
                    <button
                        type="button"
                        class="btn btn--sm btn--icon btn--light"
                        aria-label="{{ __('Close') }}"
                        x-on:click="open = false"
                    >
                        <x-root::icon name="close" class="btn__icon" />
                    </button>
                </div>
                <div class="app-notification__list">
                    <template x-if="notifications.length === 0">
                        <div class="notification-card">
                            <div class="alert alert--info">{{ __("You don't have any notifications yet.") }}</div>
                        </div>
                    </template>
                    <template x-for="notification in notifications" x-bind:key="notification.id">
                        <div
                            class="notification-card"
                            x-data="{ open: false }"
                            x-bind:class="{ 'notification-card--open': open }"
                        >
                            <div class="notification-card__header">
                                <div class="notification-card__caption">
                                    <h3 x-bind:id="notification.id" class="notification-card__title">
                                        <template x-if="! notification.is_read">
                                            <span class="notification-dot"></span>
                                        </template>
                                        <span x-text="notification.subject"></span>
                                    </h3>
                                    <p x-text="notification.formatted_created_at"></p>
                                </div>
                                <button
                                    aria-label="{{ __('Toggle notification') }}"
                                    class="btn btn--primary btn--sm btn--icon notification-card__control"
                                    x-bind:aria-describedby="notification.id"
                                    x-on:click="open = ! open; markAsRead(notification)"
                                >
                                    <x-root::icon name="plus" class="btn__icon" />
                                </button>
                            </div>
                            <div class="notification-card__body" x-bind:aria-hidden="open ? 'false' : 'true'">
                                <div x-html="notification.message"></div>
                            </div>
                        </div>
                    </template>
                </div>
                <template x-if="nextPageUrl">
                    <button type="button" class="btn btn--primary btn--block" x-on:click="fetch">
                        {{ __('Load more') }}
                    </button>
                </template>
            </div>
        </div>
    </template>
</div>
