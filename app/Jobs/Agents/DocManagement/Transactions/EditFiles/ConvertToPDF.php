<?php

namespace App\Jobs\Agents\DocManagement\Transactions\EditFiles;

use App\Models\DocManagement\Transactions\Checklists\TransactionChecklistItemsDocs;
use App\Models\DocManagement\Transactions\Documents\InProcess;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use mikehaertl\wkhtmlto\Pdf;

class ConvertToPDF implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    //public $tries = 5;

    protected $request;
    protected $Listing_ID;
    protected $Contract_ID;
    protected $Referral_ID;
    protected $transaction_type;
    protected $file_id;
    protected $document_id;
    protected $file_type;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($request, $Listing_ID, $Contract_ID, $Referral_ID, $transaction_type, $file_id, $document_id, $file_type)
    {
        $this -> request = $request;
        $this -> Listing_ID = $Listing_ID;
        $this -> Contract_ID = $Contract_ID;
        $this -> Referral_ID = $Referral_ID;
        $this -> transaction_type = $transaction_type;
        $this -> file_id = $file_id;
        $this -> document_id = $document_id;
        $this -> file_type = $file_type;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $request = $this -> request;
        $Listing_ID = $this -> Listing_ID;
        $Contract_ID = $this -> Contract_ID;
        $Referral_ID = $this -> Referral_ID;
        $transaction_type = $this -> transaction_type;
        $file_id = $this -> file_id;
        $document_id = $this -> document_id;
        $file_type = $this -> file_type;
        $uuid = $this -> job -> uuid();

        // add to in_process table
        $in_process = new InProcess();
        $in_process -> document_id = $document_id;
        //$in_process -> uuid = $uuid;
        $in_process -> save();

        $path = [
            'listing' => 'listings/'.$Listing_ID,
            'contract' => 'contracts/'.$Contract_ID,
            'referral' => 'referrals/'.$Referral_ID,
        ][$transaction_type];

        $upload_dir = 'doc_management/transactions/'.$path.'/'.$file_id.'_'.$file_type;

        Storage::makeDirectory($upload_dir.'/combined/');
        Storage::makeDirectory($upload_dir.'/layers/');
        $full_path_dir = Storage::path($upload_dir);
        $pdf_output_dir = Storage::path($upload_dir.'/combined/');

        // get file name to use for the final converted file
        $file = glob($full_path_dir.'/converted/*pdf');

        $filename = basename($file[0]);

        // create or clear out directories if they already exist
        $clean_dir = new Filesystem;
        $clean_dir -> cleanDirectory('storage/'.$upload_dir.'/layers');
        $clean_dir -> cleanDirectory('storage/'.$upload_dir.'/combined');
        //$clean_dir -> cleanDirectory('storage/'.$upload_dir.'/converted');
        exec('cp '.$file[0].' '.$full_path_dir.'/converted/backup.pdf');
        //exec('rm '.$file[0]);

        // pdf options - more added below depending on page size
        $options = [
            //'binary' => '/usr/bin/xvfb-run -- /usr/bin/wkhtmltopdf',
            'no-outline',
            'margin-top' => 0,
            'margin-right' => 0,
            'margin-bottom' => 0,
            'margin-left' => 0,
            'encoding' => 'UTF-8',
            'dpi' => 96,
            'disable-smart-shrinking',
            'tmpDir' => '/var/www/tmp',
        ];

        // loop through all pages
        for ($c = 1; $c <= $request['page_count']; $c++) {
            $page_number = $c;

            if (strlen($c) == 1) {
                $page_number = '0'.$c;
            }

            // set layer and combined directories
            $layer_pdf = $full_path_dir.'/pages/page_'.$page_number.'.pdf';
            $layer_top = $full_path_dir.'/layers/layer_top_'.$page_number.'.pdf';
            $layer_top_temp = $full_path_dir.'/layers/temp_layer_top_'.$page_number.'.pdf';
            $layer_bottom = $full_path_dir.'/layers/layer_bottom_'.$page_number.'.pdf';

            $combined_top = $pdf_output_dir.'/combined_top_'.$page_number.'.pdf';
            $combined = $pdf_output_dir.'/combined_'.$page_number.'.pdf';

            $page_width = get_width_height($layer_pdf)['width'];
            $page_height = get_width_height($layer_pdf)['height'];

            // if not standard 612 by 792 get width and height and convert to mm
            if ($page_width == 612 && $page_height == 792) {
                $options['page-size'] = 'Letter';
            } elseif ($page_width == 595 && $page_height == 842) {
                $options['page-size'] = 'a4';
            } else {
                $page_width = $page_width * 0.2745833333;
                $page_height = $page_height * 0.2745833333;

                $options['page-width'] = $page_width.'mm';
                $options['page-height'] = $page_height.'mm';
            }

            $html = "
            <style>
            @import url('https://fonts.googleapis.com/css2?family=Roboto+Condensed&display=swap');
            * {
                font-family: 'Roboto Condensed', sans-serif;
            }
            </style>
            ";

            $html_top = '';
            $html_bottom = '';

            if (isset($request['page_html_top_'.$c])) {
                $html_top = $html.$request['page_html_top_'.$c];

                $pdf = new \mikehaertl\wkhtmlto\Pdf($options);
                $pdf -> addPage($html_top);

                if (! $pdf -> saveAs($layer_top_temp)) {
                    $error = $pdf -> getError();
                    dd($error);
                }
            }

            if (isset($request['page_html_bottom_'.$c])) {
                $html_bottom = $html.$request['page_html_bottom_'.$c];

                $pdf = new \mikehaertl\wkhtmlto\Pdf($options);
                $pdf -> addPage($html_bottom);

                if (! $pdf -> saveAs($layer_bottom)) {
                    $error = $pdf -> getError();
                    dd($error);
                }
            }

            if ($html_top != '') {
                // remove background and resize top layer
                exec('convert -quality 100 -density 300 '.$layer_top_temp.' -size '.$page_width.'x'.$page_height.' -strip -transparent white -compress Zip '.$layer_top);
                // merge top pdf layer with top layer
                exec('pdftk '.$layer_top.' background '.$layer_pdf.' output '.$combined_top.' compress');
                // if not bottom move combined_top to combined
                if ($html_bottom == '') {
                    exec('mv '.$combined_top.' '.$combined);
                }
                // remove top layer file
                exec('rm '.$layer_top);
            }

            if ($html_bottom != '') {
                // if html_top add it to pdf-top layer
                if ($html_top != '') {
                    exec('pdftk '.$combined_top.' background '.$layer_bottom.' output '.$combined.' compress');
                    exec('rm '.$combined_top);
                    exec('rm '.$layer_bottom);
                } else {
                    exec('pdftk '.$layer_pdf.' background '.$layer_bottom.' output '.$combined.' compress');
                    exec('rm '.$layer_pdf);
                    exec('rm '.$layer_bottom);
                }
            }

            // if no fields to add to page
            if ($html_top == '' && $html_bottom == '') {
                exec('cp '.$layer_pdf.' '.$combined);
            }
        }

        // merge all from combined and add final to converted - named $filename
        exec('pdftk '.$full_path_dir.'/combined/*pdf cat output '.$full_path_dir.'/converted/'.$filename.' compress');

        if (file_exists($full_path_dir.'/converted/'.$filename)) {
            exec('rm '.$full_path_dir.'/converted/backup.pdf');
        }

        $checklist_item_docs_model = new TransactionChecklistItemsDocs();
        $image_filename = str_replace('.pdf', '.jpg', $filename);
        $source = $full_path_dir.'/converted/'.$filename;
        $destination = $full_path_dir.'/converted_images';
        $checklist_item_docs_model -> convert_doc_to_images($source, $destination, $image_filename, $file_id);

        // remove from in_process
        $remove_in_process = InProcess::where('document_id', $document_id) -> delete();
    }
}
