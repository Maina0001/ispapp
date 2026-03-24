<?php

namespace App\Modules\Customer\Services;

use App\Modules\Customer\Models\Customer;
use App\Modules\Customer\Models\CustomerDocument;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class DocumentService
{
    public function upload(Customer $customer, UploadedFile $file, string $type): CustomerDocument
    {
        // Store in a private disk (not public!) for security
        $path = $file->store("customers/{$customer->id}/documents", 'private');

        return CustomerDocument::create([
            'customer_id'   => $customer->id,
            'document_type' => $type,
            'file_path'     => $path,
            'file_name'     => $file->getClientOriginalName(),
            'mime_type'     => $file->getMimeType(),
        ]);
    }
}