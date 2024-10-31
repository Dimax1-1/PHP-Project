<?php
function traduciMese($data) {
    $mesi = [
        'January' => 'Gennaio',
        'February' => 'Febbraio',
        'March' => 'Marzo',
        'April' => 'Aprile',
        'May' => 'Maggio',
        'June' => 'Giugno',
        'July' => 'Luglio',
        'August' => 'Agosto',
        'September' => 'Settembre',
        'October' => 'Ottobre',
        'November' => 'Novembre',
        'December' => 'Dicembre'
    ];
    $mese_inglese = date('F', strtotime($data));
    return $mesi[$mese_inglese] . ' ' . date('Y', strtotime($data));
}
?>