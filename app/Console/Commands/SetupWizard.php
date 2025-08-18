<?php
namespace IXP\Console\Commands;

define('strict_types', 1);

/*
 * Copyright (C) 2009 - 2025 Internet Neutral Exchange Association Company Limited By Guarantee.
 * All Rights Reserved.
 *
 * This file is part of IXP Manager.
 *
 * IXP Manager is free software: you can redistribute it and/or modify it
 * under the terms of the GNU General Public License as published by the Free
 * Software Foundation, version v2.0 of the License.
 *
 * IXP Manager is distributed in the hope that it will be useful, but WITHOUT
 * ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or
 * FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General Public License for
 * more details.
 *
 * You should have received a copy of the GNU General Public License v2.0
 * along with IXP Manager.  If not, see:
 *
 * http://www.gnu.org/licenses/gpl-2.0.html
 */
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Carbon;


use IXP\Models\{CompanyBillingDetail, CompanyRegisteredDetail, Customer, CustomerToUser, Infrastructure, User};
use function Termwind\ask;

/**
 * Artisan command to streamline the initial installation of IXP Manager
 *
 * @author     Iskren Hadzhinedev <i.hadzhinedev@gmail.com>
 * @author     Barry O'Donovan <barry@opensolutions.ie>
 * @package    IXP\Console\Commands
 * @copyright  Copyright (C) 2009 - 2025 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class SetupWizard extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ixp-manager:setup-wizard'
        . ' {--ixp-name= : The name of the IXP (e.g. Somecity Internet Exchange Point)}'
        . ' {--ixp-shortname= : The short name of the IXP (e.g. DIX)}'
        . ' {--admin-name= : The name of the admin user}'
        . ' {--admin-username= : The username of the admin user}'
        . ' {--admin-password= : The password for the admin user (if unset, taken from the IXP_SETUP_ADMIN_PASSWORD environment variable or random value assigned)}'
        . ' {--admin-email= : The email of the admin user}'
        . ' {--asn= : The ASN of your IXP}'
        . ' {--ixp-email= : The contact email for your IXP (e.g. operations@example.com)}'
        . ' {--ixp-phone= : The contact number for your IXP (e.g. +353209122000)}'
        . ' {--ixp-url= : The web address for your IXP (e.g. https://www.example.com/)}'
        . ' {--echo-password : Echo the password to the console}'
        . ' {--skip-confirm : Skip the confirmation prompt}'
        . ' {--force : Force the installation without validation}';

    /**
     * The console command description.
     * @var string
     */
    protected $description = "Create initial database objects for IXP Manager";

    protected array $ixpdata = [
        'ixp-name' => [ 'var' => 'ixpname', 'default' => 'Somecity Internet Exchange Point', 'prompt' => 'Enter the full name of your IXP' ],
        'ixp-shortname' => [ 'var' => 'ixpshortname', 'default' => 'SCIX', 'prompt' => 'Enter the short name of your IXP' ],
        'admin-name' => [ 'var' => 'adminname', 'default' => 'Joe Bloggs', 'prompt' => 'Enter the full name of the admin user' ],
        'admin-username' => [ 'var' => 'adminusername', 'default' => 'jbloggs', 'prompt' => 'Enter the username of the admin user' ],
        'admin-password' => [ 'var' => 'adminpassword', 'default' => null, 'prompt' => 'Enter the password of the admin user' ],
        'admin-email' => [ 'var' => 'adminemail', 'default' => 'joebloggs@example.com', 'prompt' => 'Enter the email of the admin user' ],
        'asn' => [ 'var' => 'asn', 'default' => '65535', 'prompt' => 'Enter the ASN of your IXP' ],
        'ixp-email' => [ 'var' => 'ixpemail', 'default' => 'operations@example.com', 'prompt' => 'Enter the email of the IXP' ],
        'ixp-phone' => [ 'var' => 'ixpphone', 'default' => '+353 20 912 2000', 'prompt' => 'Enter the phone number of the IXP' ],
        'ixp-url' => [ 'var' => 'ixpurl', 'default' => 'https://www.example.com/', 'prompt' => 'Enter the web address of the IXP' ],
    ];


    /**
     * Execute the console command.
     *
     * @return int
     *
     * @throws
     */
    public function handle(): int
    {
        if (Customer::count() > 0) {
            $this->error('IXP Manager has already been setup. Exiting.');
            return 1;
        }

        $this->line( self::BANNER );

        $this->line("Welcome to the IXP Manager setup wizard!\n\n");

        $table      = [];
        $data       = [];

        // gather data
        foreach( $this->ixpdata as $option => $value ) {

            if( $this->option( $option ) ) {
                ${$value['var']} = $this->option( $option );
            } else {

                if( $option === 'admin-password' ) {

                    if( ( $envPassword = getenv('IXP_SETUP_ADMIN_PASSWORD') ) !== false) {
                        // Do not use laravel's `env()` because it reads the .env file.
                        ${$value['var']} = getenv('IXP_SETUP_ADMIN_PASSWORD');

                        // Unset the variable as soon as we read it to reduce the risk of it leaking.
                        putenv( 'IXP_SETUP_ADMIN_PASSWORD' );
                    } else {
                        ${$value['var']} = str()->random( 12 );
                    }

                } else {

                    ${$value[ 'var' ]} = ask( $value[ 'prompt' ] . ' [' . $value[ 'default' ] . '] ' );

                    if( !${$value[ 'var' ]} ) {
                        ${$value[ 'var' ]} = $value[ 'default' ];
                    }
                }



            }

            $data[ $option ] = ${$value[ 'var' ]};

            if( $option !== 'admin-password' || $this->option( 'echo-password' ) ) {
                $table[] = [ $option, ${$value[ 'var' ]} ];
            }

        }

        $this->table( ['Option', 'Value'], $table );

        $validator = \Validator::make($data, [
            'asn' => 'required|integer|between:1,4294967295',
            'ixp-name' => 'required|string',
            'ixp-shortname' => 'required|string',
            'admin-name' => 'required|string',
            'admin-username' => 'required|string',
            'admin-email' => 'required|email',
            'admin-password' => 'required|string|min:10',
            'ixp-phone' => 'required|string',
            'ixp-url' => 'required|url',
            'ixp-email' => 'required|email',
        ]);

        if ( !$this->option('force') && $validator->fails()) {
            $this->error('The following errors occurred:');
            foreach ($validator->errors()->all() as $error) {
                $this->error("\t" . $error);
            }
            return 2;
        }

        if( ( !$this->option('force') || !$this->option( 'skip-confirm' ) )
                && !$this->confirm( 'Is this information correct, and do you want to continue to create the database objects?' ) )
        {
            $this->error('No confirmation was given. Exiting.');
            return 3;
        }

        try {

            DB::beginTransaction();

            Infrastructure::create( [
                'name'       => $ixpname,
                'shortname'  => $ixpshortname,
                'isPrimary'  => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ] );

            $billingDetail = CompanyBillingDetail::create( [
                'billingContatName' => $adminname,
                'billingEmail'      => $adminemail,
                'invoiceMethod'     => CompanyBillingDetail::INVOICE_METHOD_EMAIL,
                'billingFrequency'  => CompanyBillingDetail::BILLING_FREQUENCY_NOBILLING,
                'created_at'        => Carbon::now(),
                'updated_at'        => Carbon::now(),
            ] );

            $registrationDetail = CompanyRegisteredDetail::create( [
                'registeredName' => $ixpname,
                'created_at'     => Carbon::now(),
                'updated_at'     => Carbon::now(),
            ] );

            $cust = Customer::create( [
                'name'                         => $ixpname,
                'type'                         => Customer::TYPE_INTERNAL,
                'shortname'                    => $ixpshortname,
                'autsys'                       => $asn,
                'maxprefixes'                  => 1,
                'peeringemail'                 => $ixpemail,
                'peeringpolicy'                => Customer::PEERING_POLICY_MANDATORY,
                'nocphone'                     => $ixpphone,
                'noc24hphone'                  => $ixpphone,
                'nocemail'                     => $ixpemail,
                'nochours'                     => Customer::NOC_HOURS_24x7,
                'nocwww'                       => $ixpurl,
                'corpwww'                      => $ixpurl,
                'datejoin'                     => Carbon::now(),
                'status'                       => Customer::STATUS_NORMAL,
                'activepeeringmatrix'          => true,
                'company_registered_detail_id' => $registrationDetail->id,
                'company_billing_details_id'   => $billingDetail->id,
                'abbreviatedName'              => $ixpshortname,
                'isReseller'                   => false,
                'created_at'                   => Carbon::now(),
                'updated_at'                   => Carbon::now(),
            ] );

            $cust->contacts()->create( [
                'name'       => $adminname,
                'email'      => $adminemail,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ] );

            $user = new User;
            $user->name = $adminname;
            $user->username = $adminusername;
            $user->password = password_hash( $adminpassword, PASSWORD_BCRYPT, [ 'cost' => config( 'hashing.bcrypt.rounds' ) ] );
            $user->email = $adminemail;
            $user->privs = User::AUTH_SUPERUSER;
            $user->disabled = false;
            $user->creator = 'IXP Manager SetupWizard';
            $user->created_at = Carbon::now();
            $user->updated_at = Carbon::now();
            $user->save();

            CustomerToUser::create( [
                'customer_id' => $cust->id,
                'user_id'     => $user->id,
                'privs'       => User::AUTH_SUPERUSER,
                'created_at'  => Carbon::now(),
                'updated_at'  => Carbon::now(),
            ] );

            DB::commit();

        } catch (\Exception $e) {
            $this->error('A database error occurred while setting up IXP Manager:' . $e->getMessage());
            return 4;
        }

        $this->info('IXP Manager has been setup successfully!');

        return 0;
    }



    public const string BANNER = "

 _____  ______    __  __                                   
|_ _\ \/ /  _ \  |  \/  | __ _ _ __   __ _  __ _  ___ _ __ 
 | | \  /| |_) | | |\/| |/ _` | '_ \ / _` |/ _` |/ _ \ '__|
 | | /  \|  __/  | |  | | (_| | | | | (_| | (_| |  __/ |   
|___/_/\_\_|_    |_|  |_|\__,_|_| |_|\__,_|\__, |\___|_|_  
/ ___|  ___| |_ _   _ _ _\ \      / (_)____|___/_ __ __| | 
\___ \ / _ \ __| | | | '_ \ \ /\ / /| |_  / _` | '__/ _` | 
 ___) |  __/ |_| |_| | |_) \ V  V / | |/ / (_| | | | (_| | 
|____/ \___|\__|\__,_| .__/ \_/\_/  |_/___\__,_|_|  \__,_| 
                     |_|                                   

";

}