{{-- Emoji picker script --}}
<script>
(function() {
    function initEmojiPickers() {
        document.querySelectorAll('.emoji-toggle-btn').forEach(function(btn) {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                var popup = this.parentElement.querySelector('.emoji-popup');
                document.querySelectorAll('.emoji-popup.show').forEach(function(p) {
                    if (p !== popup) p.classList.remove('show');
                });
                popup.classList.toggle('show');
            });
        });

        document.querySelectorAll('emoji-picker').forEach(function(picker) {
            picker.addEventListener('emoji-click', function(e) {
                var wrapper = this.closest('.emoji-picker-wrapper');
                var targetId = wrapper.getAttribute('data-target');
                var textarea = document.getElementById(targetId) || wrapper.closest('form').querySelector('textarea, input[type="text"]');
                if (textarea) {
                    var start = textarea.selectionStart;
                    var end = textarea.selectionEnd;
                    var text = textarea.value;
                    textarea.value = text.substring(0, start) + e.detail.unicode + text.substring(end);
                    textarea.selectionStart = textarea.selectionEnd = start + e.detail.unicode.length;
                    textarea.focus();
                }
                wrapper.querySelector('.emoji-popup').classList.remove('show');
            });
        });

        document.addEventListener('click', function(e) {
            if (!e.target.closest('.emoji-picker-wrapper')) {
                document.querySelectorAll('.emoji-popup.show').forEach(function(p) {
                    p.classList.remove('show');
                });
            }
        });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function() {
            setTimeout(initEmojiPickers, 500);
        });
    } else {
        setTimeout(initEmojiPickers, 500);
    }
})();
</script>
