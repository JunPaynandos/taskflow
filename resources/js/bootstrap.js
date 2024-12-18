import axios from "axios";
// import Echo from 'laravel-echo';
// import Pusher from 'pusher-js';

window.axios = axios;

window.axios.defaults.headers.common["X-Requested-With"] = "XMLHttpRequest";

// window.Pusher = Pusher;
// window.Echo = new Echo({
//     broadcaster: 'pusher',
//     key: '10b73f07e78d992ee84c',
//     cluster: 'ap1',
//     encrypted: true
// });

import Echo from "laravel-echo";
import Pusher from "pusher-js";

window.Pusher = Pusher;
window.Echo = new Echo({
    broadcaster: "pusher",
    key: '10b73f07e78d992ee84c',
    cluster: 'ap1',
    forceTLS: true,
    debug: true,
});
