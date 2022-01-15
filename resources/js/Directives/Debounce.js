import { debounce } from './../Support/Helpers';

export default {
    beforeMount(el, binding) {
        if (binding.value !== binding.oldValue) {
            el.oninput = debounce((event) => {
                el.dispatchEvent(new Event('change'));
            }, parseInt(binding.value) || 300);
        }
    },
}
