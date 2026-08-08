import Alpine from 'alpinejs';
window.Alpine = Alpine;

Alpine.store('confirmDialog', {
    show: false,
    variant: 'danger',
    title: '',
    message: '',
    confirmLabel: 'Confirmer',
    pendingForm: null,

    confirm() {
        if (this.pendingForm) {
            this.pendingForm.submit();
        }
        this.show = false;
        this.pendingForm = null;
    },

    cancel() {
        this.show = false;
        this.pendingForm = null;
    },
});

Alpine.start();

document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('form').forEach(form => {
        form.noValidate = true;
    });
});
