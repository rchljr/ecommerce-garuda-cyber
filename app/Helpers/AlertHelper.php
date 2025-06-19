<?php

/**
 * File helper untuk menampilkan notifikasi dan konfirmasi SweetAlert2.
 */

if (!function_exists('showAlert')) {
    /**
     * Merender Toast SweetAlert2 dari session.
     */
    function showAlert(): string
    {
        $alertTypes = ['success', 'error', 'warning', 'info'];
        $html = '';

        foreach ($alertTypes as $type) {
            if (session()->has($type)) {
                $message = json_encode(session($type));
                $html .= "
                    <script>
                        document.addEventListener('DOMContentLoaded', function() {
                            const Toast = Swal.mixin({
                                toast: true,
                                position: 'top-end',
                                showConfirmButton: false,
                                timer: 3500,
                                timerProgressBar: true,
                                didOpen: (toast) => {
                                    toast.addEventListener('mouseenter', Swal.stopTimer);
                                    toast.addEventListener('mouseleave', Swal.resumeTimer);
                                }
                            });
                            Toast.fire({ icon: '{$type}', title: {$message} });
                        });
                    </script>
                ";
                break;
            }
        }
        return $html;
    }
}

if (!function_exists('deleteConfirmScript')) {
    /**
     * Render script global untuk konfirmasi HAPUS.
     * Target: Tombol dengan class .delete-btn di dalam sebuah form.
     */
    function deleteConfirmScript(): string
    {
        return <<<HTML
<script>
document.addEventListener('DOMContentLoaded', function() {
    document.body.addEventListener('click', function(e) {
        if (e.target.closest('.delete-btn')) {
            e.preventDefault();
            const form = e.target.closest('form');
            Swal.fire({
                title: 'Yakin ingin menghapus?',
                text: 'Tindakan ini tidak dapat dibatalkan!',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        }
    });
});
</script>
HTML;
    }
}

if (!function_exists('deactivateConfirmScript')) {
    /**
     * DITAMBAHKAN: Render script global untuk konfirmasi NONAKTIFKAN.
     * Target: Form dengan class .deactivate-form.
     */
    function deactivateConfirmScript(): string
    {
        return <<<HTML
<script>
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.deactivate-form').forEach(function(form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            Swal.fire({
                title: 'Anda Yakin?',
                text: 'Anda akan menonaktifkan paket mitra ini.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, Nonaktifkan!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });
    });
});
</script>
HTML;
    }
}
