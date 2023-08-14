<div class="app-notification" x-data="{ open: false }" @click.outside="open = false">
    <button class="btn btn--light btn--icon btn--counter app-notification__toggle" :aria-expanded="open" @click="open = ! open" aria-label="Notifications">
        <svg aria-hidden='true' fill='none' focusable='false' height='24' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' stroke='currentColor' viewBox='0 0 24 24' width='24' xmlns='http://www.w3.org/2000/svg' class='btn__icon'>
            <path d='M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9'></path>
            <path d='M13.73 21a2 2 0 0 1-3.46 0'></path>
        </svg>
        <span class="btn__counter">4</span>
    </button>
    <div class="context-menu" :data-state="open ? 'open' : 'closed'">
        <div class="app-notification__header">
            <h2 class="app-notification__title">Notifications</h2>
            <button class="btn btn--sm btn--icon btn--light" aria-label="Close modal" @click="open = false">
                <svg aria-hidden='true' fill='none' focusable='false' height='24' stroke-linecap='round' stroke-linejoin='round' stroke-width='3' stroke='currentColor' viewBox='0 0 24 24' width='24' xmlns='http://www.w3.org/2000/svg' class='btn__icon'>
                    <line x1='18' y1='6' x2='6' y2='18'></line>
                    <line x1='6' y1='6' x2='18' y2='18'></line>
                </svg>
            </button>
        </div>
        <div class="app-notification__list">
            <div class="notification-card notification-card--unread">
                <div class="notification-card__icon notification-card__icon--info">
                    <svg aria-hidden='true' fill='none' focusable='false' height='24' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' stroke='currentColor' viewBox='0 0 24 24' width='24' xmlns='http://www.w3.org/2000/svg' class='icon'>
                        <circle cx='9' cy='21' r='1'></circle>
                        <circle cx='20' cy='21' r='1'></circle>
                        <path d='M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6'></path>
                    </svg>
                </div>
                <div class="notification-card__caption">
                    <h3 class="notification-card__title">
                        <a href="#" class="notification-card__link">
                            Important Weekly Team Meeting
                        </a>
                    </h3>
                    <p>August 9, 2023 12:00</p>
                </div>
            </div>
            <div class="notification-card notification-card--unread">
                <div class="notification-card__icon notification-card__icon--danger">
                    <svg aria-hidden='true' fill='none' focusable='false' height='24' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' stroke='currentColor' viewBox='0 0 24 24' width='24' xmlns='http://www.w3.org/2000/svg' class='icon'>
                        <polyline points='3 6 5 6 21 6'></polyline>
                        <path d='M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2'></path>
                        <line x1='10' y1='11' x2='10' y2='17'></line>
                        <line x1='14' y1='11' x2='14' y2='17'></line>
                    </svg>
                </div>
                <div class="notification-card__caption">
                    <h3 class="notification-card__title">
                        <a href="#" class="notification-card__link">
                            Upcoming Payment Deadline for Monthly Subscription
                        </a>
                    </h3>
                    <p>August 10, 2023 17:30</p>
                </div>
            </div>
            <div class="notification-card notification-card--unread">
                <div class="notification-card__icon notification-card__icon--success">
                    <svg aria-hidden='true' fill='none' focusable='false' height='24' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' stroke='currentColor' viewBox='0 0 24 24' width='24' xmlns='http://www.w3.org/2000/svg' class='icon'>
                        <path d='M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2'></path>
                        <circle cx='9' cy='7' r='4'></circle>
                        <path d='M23 21v-2a4 4 0 0 0-3-3.87'></path>
                        <path d='M16 3.13a4 4 0 0 1 0 7.75'></path>
                    </svg>
                </div>
                <div class="notification-card__caption">
                    <h3 class="notification-card__title">
                        <a href="#" class="notification-card__link">
                            25th Wedding Anniversary Celebration at Grand Hotel
                        </a>
                    </h3>
                    <p>August 15, 2023 20:00</p>
                </div>
            </div>
            <div class="notification-card notification-card--unread">
                <div class="notification-card__icon notification-card__icon--warning">
                    <svg aria-hidden='true' fill='none' focusable='false' height='24' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' stroke='currentColor' viewBox='0 0 24 24' width='24' xmlns='http://www.w3.org/2000/svg' class='icon'>
                        <path d='M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z'></path>
                        <line x1='12' y1='9' x2='12' y2='13'></line>
                        <line x1='12' y1='17' x2='12.01' y2='17'></line>
                    </svg>
                </div>
                <div class="notification-card__caption">
                    <h3 class="notification-card__title">
                        <a href="#" class="notification-card__link">
                            Exclusive Webinar: Unveiling the Latest Industry Trends
                        </a>
                    </h3>
                    <p>August 20, 2023 11:30</p>
                </div>
            </div>
            <div class="notification-card notification-card--read">
                <div class="notification-card__icon notification-card__icon--info">
                    <svg aria-hidden='true' fill='none' focusable='false' height='24' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' stroke='currentColor' viewBox='0 0 24 24' width='24' xmlns='http://www.w3.org/2000/svg' class='icon'>
                        <circle cx='9' cy='21' r='1'></circle>
                        <circle cx='20' cy='21' r='1'></circle>
                        <path d='M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6'></path>
                    </svg>
                </div>
                <div class="notification-card__caption">
                    <h3 class="notification-card__title">
                        <a href="#" class="notification-card__link">
                            Final Submission Deadline for Project Proposal
                        </a>
                    </h3>
                    <p>August 18, 2023 14:00</p>
                </div>
            </div>
            <div class="notification-card notification-card--read">
                <div class="notification-card__icon notification-card__icon--info">
                    <svg aria-hidden='true' fill='none' focusable='false' height='24' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' stroke='currentColor' viewBox='0 0 24 24' width='24' xmlns='http://www.w3.org/2000/svg' class='icon'>
                        <circle cx='9' cy='21' r='1'></circle>
                        <circle cx='20' cy='21' r='1'></circle>
                        <path d='M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6'></path>
                    </svg>
                </div>
                <div class="notification-card__caption">
                    <h3 class="notification-card__title">
                        <a href="#" class="notification-card__link">
                            Departure for Exciting Adventure: Traveling to Exotic Destinations
                        </a>
                    </h3>
                    <p>August 25, 2023 10:00</p>
                </div>
            </div>
            <div class="notification-card notification-card--read">
                <div class="notification-card__icon notification-card__icon--info">
                    <svg aria-hidden='true' fill='none' focusable='false' height='24' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' stroke='currentColor' viewBox='0 0 24 24' width='24' xmlns='http://www.w3.org/2000/svg' class='icon'>
                        <circle cx='9' cy='21' r='1'></circle>
                        <circle cx='20' cy='21' r='1'></circle>
                        <path d='M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6'></path>
                    </svg>
                </div>
                <div class="notification-card__caption">
                    <h3 class="notification-card__title">
                        <a href="#" class="notification-card__link">
                            Highly Anticipated Product Release: Introducing InnovationX
                        </a>
                    </h3>
                    <p>August 30, 2023 16:00</p>
                </div>
            </div>
        </div>
        <a href="#" class="btn btn--primary btn--block">View all notifications</a>
    </div>
</div>
