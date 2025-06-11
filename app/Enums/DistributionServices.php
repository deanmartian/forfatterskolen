<?php

namespace App\Enums;

use App\Traits\EnumsWithOptions;

class DistributionServices
{
    use EnumsWithOptions;

    const order_line_debit_bookstore = 'Ordrelinje debet bokhandel';

    const weight_books_debit_bookstore = 'Vekt bøker debet bokhandel';

    const order_line_free_withdrawal = 'Ordrelinje frieks uttak';

    const weight_books_freeks_withdrawal = 'Vekt bøker frieks uttak';

    const order_line_credit = 'Ordrelinje kredit';

    const weight_books_credit = 'Vekt bøker kredit';

    const storage_fee_per_isbn_no = 'Lagerholdsavgift pr ISBN-nr';

    const title_fee_per_isbn_no = 'Tittelavgift pr ISBN-nr';

    const freight_bookstore = 'Frakt bokhandel';
}
