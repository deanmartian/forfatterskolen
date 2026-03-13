<?php

namespace Tests\Feature\Api\V1;

use Tests\TestCase;

class InvoiceReceiptDownloadApiTest extends TestCase
{
    public function test_learner_invoice_receipt_download_route_requires_token(): void
    {
        $response = $this->getJson('/api/v1/learner/invoice/1/receipt/download');

        $response->assertStatus(401)
            ->assertJsonStructure([
                'error' => ['message', 'code'],
                'request_id',
            ]);
    }

    public function test_learner_invoice_receipt_download_named_route_matches_expected_path(): void
    {
        $url = route('learner.invoice.receipt.download', ['id' => 99], false);

        $this->assertSame('/api/v1/learner/invoice/99/receipt/download', $url);
    }
}
