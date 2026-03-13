<?php

namespace Tests\Feature\Api\V1;

use Tests\TestCase;

class InvoicePdfViewApiTest extends TestCase
{
    public function test_invoice_pdf_view_route_requires_token(): void
    {
        $response = $this->getJson('/api/v1/invoices/1/pdf/view');

        $response->assertStatus(401)
            ->assertJsonStructure([
                'error' => ['message', 'code'],
                'request_id',
            ]);
    }

    public function test_invoice_pdf_view_named_route_matches_expected_path(): void
    {
        $url = route('api.invoices.pdf.view', ['id' => 99], false);

        $this->assertSame('/api/v1/invoices/99/pdf/view', $url);
    }
}
