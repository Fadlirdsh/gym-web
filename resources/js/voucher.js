// Pastikan voucherStoreUrl sudah didefinisikan di blade sebelum ini

document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('formAddVoucher');

    form.addEventListener('submit', function(e) {
        e.preventDefault();

        const formData = new FormData(form);

        fetch(voucherStoreUrl, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': formData.get('_token')
            },
            body: formData
        })
        .then(async res => {
            if (!res.ok) throw res;
            return res.json();
        })
        .then(res => {
            alert('Voucher berhasil ditambahkan');
            location.reload();
        })
        .catch(async err => {
            let msg = 'Terjadi kesalahan. Cek console.';
            try {
                const data = await err.json();
                if(data.errors){
                    msg = Object.values(data.errors).flat().join("\n");
                } else if(data.message){
                    msg = data.message;
                }
            } catch(e){
                console.error(err);
            }
            alert(msg);
        });
    });
});
