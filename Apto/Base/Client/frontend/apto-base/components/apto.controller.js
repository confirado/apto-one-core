export default class Controller {
    constructor() {
        // event listener
        this.eventListeners = [];
    }

    $onDestroy() {
        // destroy all listeners
        for (let i = 0; i < this.eventListeners.length; i++) {
            this.eventListeners[i]();
        }
    };
}
