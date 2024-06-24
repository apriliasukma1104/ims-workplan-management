function replaceDotWithComma(input) {
    var numberInput = parseFloat(input);

    if (Number.isInteger(numberInput)) {  // Mengecek apakah memiliki nilai desimal
        return numberInput.toString();
    } else {
        var fixedNumber = numberInput.toFixed(2); // Jika memiliki nilai desimal, mengatur pembulatan hingga 2 tempat desimal
        var stringInput = fixedNumber.toString();
        var replacedString = stringInput.replace(/\./g, ',');
        return replacedString;
    }
}

function formatRupiah(angka, prefix="Rp"){
    var number_string = angka.replace(/[^,\d]/g, '').toString(),
    split   = number_string.split(','),
    sisa     = split[0].length % 3,
    rupiah     = split[0].substr(0, sisa),
    ribuan     = split[0].substr(sisa).match(/\d{3}/gi);
    
    // tambahkan titik jika yang di input sudah menjadi angka ribuan
    if(ribuan){
    var separator = sisa ? '.' : '';
    rupiah += separator + ribuan.join('.') + ',00';
    }
    
    rupiah = split[1] != undefined ? rupiah + ',' + split[1] : rupiah;
    return prefix == undefined ? rupiah : (rupiah ? 'Rp' + rupiah : '');
}

export {
    replaceDotWithComma,
    formatRupiah
}