
<script  async>
    window.Laravel = '{{ json_encode(['csrfToken' => csrf_token()]) }}';
</script>

<script type="text/javascript">
    window.GUMLET_CONFIG = {
        hosts: [{
            current: "https://www.forfatterskolen.no/",
            gumlet: "forfatterskolen.gumlet.com"
        }]
    };
</script>
<script async src="https://cdn.gumlet.com/gumlet.js/2.0/gumlet.min.js"></script>