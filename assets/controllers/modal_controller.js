import { Controller } from '@hotwired/stimulus';

/*
 * This is an example Stimulus controller!
 *
 * Any element with a data-controller="hello" attribute will cause
 * this controller to be executed. The name "hello" comes from the filename:
 * hello_controller.js -> "hello"
 *
 * Delete this file or adapt it for your use!
 */
export default class extends Controller {
    static targets = ["modal", "overlay", "openModal"];

    connect() {
        this.modalTarget.classList.remove('active');
        this.overlayTarget.classList.remove('active');
    }

    open() {
        this.modalTarget.classList.add('active');
        this.overlayTarget.classList.add('active');
    }

    close() {
        this.modalTarget.classList.remove('active');
        this.overlayTarget.classList.remove('active');
    }
}