<?php
    /*
    |--------------------------------------------------------------------------
    | Database Tables/Fields Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines used during application launch for various 
    | database fields that we need to display to the user. You are free to modify
    | these language lines according to your application's requirements.
    |
    */
return [
    'admincomposer' => [
        'title' => 'Send Messages',
        'menu' => 'Send Message',
        'create' => 'Composer Message',
        'update' => 'Update Message',
        'details' => 'Message Details',
        'field' => [
            'id'	=> 'ID',
            'message_type'	=> 'Message Type',
            'to'	=> 'To',
            'user_email'	=> 'User Email',
            'from'	=> 'From',
            'title'	=> 'Title',
            'message'	=> 'Message',
            'created_at'	=> 'Date',
            'updated_at'	=> 'Updated',
            'message_by'	=> 'By',
            'document'	=> 'Attachment',
        ]
    ],
	'customers' => [
        'title' => 'Customers',
        'menu' => 'Customers',
        'create' => 'Customers - Add',
        'update' => 'Customers - Update',
        'details' => 'Customers - Details',
        'field' => [
	        'contact_id'	=> 'Contact Id',
	        'user_id'	=> 'User Id',
	        'customer_account'	=> 'Customer Account',
	        'customer_email'	=> 'Customer Email',
            'customer_email_bcc'	=> 'BCC Email',
	        'customer_phone'	=> 'Customer Phone',
	        'terms_of_payment'	=> 'Customer Specific Message',
	        'message_type'	=> 'Message Type',
	        'po_number'	=> 'Po Number',
        ]
    ],

	'invoices' => [
        'title' => 'Invoices',
        'menu' => 'Invoices',
        'create' => 'Invoices - Add',
        'update' => 'Invoices - Update',
        'details' => 'Invoices - Details',
        'field' => [
	        'invoice_id'	=> 'Invoice Id',
	        'sales_id'	=> 'Sales Id',
	        'customer_account'	=> 'Customer Account',
	        'invoice_number'	=> 'Invoice Number',
	        'invoice_date'	=> 'Invoice Date',
	        'due_date'	=> 'Due Date',
	        'date_created'	=> 'Date Created',
	        'terms'	=> 'Terms',
	        'printer'	=> 'Printer',
	        'po_number'	=> 'Po Number',
	        'num'	=> 'Num',
        ]
    ],

	'sales' => [
        'title' => 'Sales',
        'menu' => 'Sales',
        'create' => 'Sales - Add',
        'update' => 'Sales - Update',
        'details' => 'Sales - Details',
        'field' => [
	        'invoice_number'	=> 'Invoice Number',
	        'invoice_date'	=> 'Invoice Date',
	        'customer_account'	=> 'Customer Account',
	        'customer_name'	=> 'Customer Name',
	        'address1'	=> 'Address1',
	        'address2'	=> 'Address2',
	        'town'	=> 'Town',
	        'country'	=> 'Country',
	        'postcode'	=> 'Postcode',
	        'spacer1'	=> 'Spacer1',
	        'customer_account2'	=> 'Customer Account2',
	        'numb1'	=> 'Numb1',
	        'items'	=> 'Items',
	        'weight'	=> 'Weight',
	        'invoice_total'	=> 'Invoice Total',
	        'numb2'	=> 'Numb2',
	        'spacer2'	=> 'Spacer2',
	        'job_number'	=> 'Job Number',
	        'job_date'	=> 'Job Date',
	        'sending_deport'	=> 'Sending Deport',
	        'delivery_deport'	=> 'Delivery Deport',
	        'destination'	=> 'Destination',
	        'town2'	=> 'Town2',
	        'postcode2'	=> 'Postcode2',
	        'service_type'	=> 'Service Type',
	        'items2'	=> 'Items2',
	        'volume_weight'	=> 'Volume Weight',
	        'numb3'	=> 'Numb3',
	        'increased_liability_cover'	=> 'Increased Liability Cover',
	        'sub_total'	=> 'Sub Total',
	        'spacer3'	=> 'Spacer3',
	        'numb4'	=> 'Numb4',
	        'sender_reference'	=> 'Sender Reference',
	        'numb5'	=> 'Numb5',
	        'percentage_fuel_surcharge'	=> 'Percentage Fuel Surcharge',
	        'spacer4'	=> 'Spacer4',
	        'senders_postcode'	=> 'Senders Postcode',
	        'vat_amount'	=> 'Vat Amount',
	        'vat_percent'	=> 'Vat Percent',
	        'sales_id'	=> 'Sales Id',
	        'uploadcode'	=> 'Uploadcode',
	        'ms_created'	=> 'Ms Created',
	        'job_dat'	=> 'Job Dat',
        ]
    ],
    'messagesstatus' => [
        'title' => 'Messages Status',
        'menu' => 'Messages',
        'create' => 'Messages Status - Add',
        'update' => 'Messages Status - Update',
        'details' => 'Messages Status - Details',
        'field' => [
            'id'	=> 'Id',
            'message_id'	=> 'Message Id',
            'user_id'	=> 'User Id',
            'sent_status'	=> 'Sent Status',
            'sent_at'	=> 'Sent At',
        ]
    ],
	'settings' => [
        'title' => 'Settings',
        'menu' => 'Settings',
        'create' => 'Settings - Add',
        'update' => 'Settings - Update',
        'details' => 'Settings - Details',
        'field' => [
	        'id'	=> 'Id',
	        'company_name'	=> 'Company Name',
	        'logo'	=> 'Logo',
	        'company_address1'	=> 'Company Address1',
	        'company_address2'	=> 'Company Address2',
	        'state'	=> 'State',
	        'city'	=> 'City',
	        'postcode'	=> 'Postcode',
	        'country'	=> 'Country',
	        'phone'	=> 'Phone',
	        'fax'	=> 'Fax',
	        'cemail'	=> 'Cemail',
	        'website'	=> 'Website',
	        'primary_contact'	=> 'Primary Contact',
	        'base_currency'	=> 'Base Currency',
	        'vat_number'	=> 'Vat Number',
	        'invoice_due_date'	=> 'Invoice Due Date',
	        'invoice_due_payment_by'	=> 'Invoice Due Payment By',
	        'message_title'	=> 'Message Title',
	        'default_message'	=> 'Default Message',
	        'default_message2'	=> 'Default Message2',
        ]
    ],
];