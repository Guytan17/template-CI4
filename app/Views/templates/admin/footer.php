        </div>
    </div>
</div>

<?php if (!empty($messages)): ?>
<script>
$(document).ready(function() {
    <?php foreach ($messages as $message): ?>
    Swal.fire({
        toast: true,
        position: 'top-end',
        icon: '<?= esc($message['toast']) ?>',
        title: '<?= esc($message['txt']) ?>',
        showConfirmButton: false,
        timer: 3000,
        timerProgressBar: true
    });
    <?php endforeach; ?>
});
</script>
<?php endif; ?>
