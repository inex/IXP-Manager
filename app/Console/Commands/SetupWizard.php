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
        . ' {--ixp-shortname= : The short name of the IXP (e.g. DIX)}'
        . ' {--admin-name= : The name of the admin user}'
        . ' {--admin-username= : The username of the admin user}'
        . ' {--admin-password= : The password for the admin user (if unset, taken from the IXP_SETUP_ADMIN_PASSWORD environment variable or random value assigned)}'
        . ' {--admin-email= : The email of the admin user}'
        . ' {--asn= : The ASN of your IXP}'
        . ' {--echo-password : Echo the password to the console}'
        . ' {--skip-confirm : Skip the confirmation prompt}'
        . ' {--force : Force the installation without validation}';

    /**
     * The console command description.
     * @var string
     */
    protected $description = "Create initial database objects for IXP Manager";


    protected array $ixpdata = [
        'ixp-name'       => [
            'var' => 'ixpname',
            'config' => 'identity.name',
            'env' => 'IXP_NAME',
        ],
        'ixp-legalname'       => [
            'var' => 'ixplegalname',
            'config' => 'identity.legalname',
            'env' => 'IXP_LEGALNAME',
        ],
        'ixp-shortname'  => [
            'var' => 'ixpshortname',
            'default' => 'IXP',
            'prompt' => 'Enter the short name of your IXP',
            'ask' => true,
        ],
        'admin-name'     => [
            'var' => 'adminname',
            'default' => 'Joe Bloggs',
            'prompt' => 'Enter the full name of the admin user',
            'ask' => true,
        ],
        'admin-username' => [
            'var' => 'adminusername',
            'default' => 'jbloggs',
            'prompt' => 'Enter the username of the admin user',
            'ask' => true,
        ],
        'admin-password' => [
            'var' => 'adminpassword',
            'default' => null,
            'prompt' => 'Enter the password of the admin user',
            'ask' => true,
        ],
        'admin-email'    => [
            'var' => 'adminemail',
            'default' => 'joebloggs@example.com',
            'prompt' => 'Enter the email of the admin user',
            'ask' => true,
        ],
        'asn'            => [
            'var' => 'asn',
            'default' => '65535',
            'prompt' => 'Enter the ASN of your IXP',
            'ask' => true,
        ],
        'ixp-email'      => [
            'var' => 'ixpemail',
            'config' => 'identity.support_email',
            'prompt' => 'Enter the support email of the IXP',
            'env' => 'IXP_SUPPORT_EMAIL',
        ],
        'ixp-phone'      => [
            'var' => 'ixpphone',
            'config' => 'identity.support_phone',
            'prompt' => 'Enter the support phone number of the IXP',
            'env' => 'IXP_SUPPORT_PHONE',
        ],
        'ixp-billing-email'      => [
            'var' => 'ixpbillingemail',
            'config' => 'identity.billing_email',
            'prompt' => 'Enter the billing email of the IXP',
            'env' => 'IXP_BILLING_EMAIL',
        ],
        'ixp-billing-phone'      => [
            'var' => 'ixpbillingphone',
            'config' => 'identity.billing_phone',
            'prompt' => 'Enter the billing phone number of the IXP',
            'env' => 'IXP_BILLING_PHONE',
        ],
        'ixp-url'        => [
            'var' => 'ixpurl',
            'config' => 'identity.corporate_url',
            'prompt' => 'Enter the web address of the IXP',
            'env' => 'IXP_CORPORATE_URL',
        ],
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

        // The premise of this script is to create the necessary database objects for IXP Manager
        // after a new installation.
        //
        // One key element is that a number of settings, which the script will inform and ask for
        // confirmation of, are set in the .env file.
        //
        // A handful of others are prompted from the user. In particular, the detaails for the
        // first admin user are asked for.
        //
        // This admin user's password should preferably be set in the IXP_SETUP_ADMIN_PASSWORD
        // environment variable.



        if (Customer::count() > 0) {
            $this->error('IXP Manager has already been setup. Exiting.');
            return 1;
        }

        $this->line( self::BANNER );

        $this->line("Welcome to the IXP Manager setup wizard!\n\n");

        // print a warning to inform the user that some options are taken from .env
        if( !$this->confirmDotEnv() ) {
            return 1;
        }

        $table      = [];
        $data       = [];

        // gather data
        foreach( $this->ixpdata as $setting => $attributes ) {

            if( !isset( $attributes[ 'ask' ] ) || !$attributes[ 'ask' ] ) {
                ${$attributes[ 'var' ]} = config( $attributes[ 'config' ] );

            } elseif( $this->option( $setting ) ) {
                ${$attributes['var']} = $this->option( $setting );

            } else {

                if( $setting === 'admin-password' ) {

                    if( getenv( 'IXP_SETUP_ADMIN_PASSWORD' ) !== false ) {
                        // Do not use laravel's `env()` because it reads the .env file.
                        ${$attributes[ 'var' ]} = getenv( 'IXP_SETUP_ADMIN_PASSWORD' );

                        // Unset the variable as soon as we read it to reduce the risk of it leaking.
                        putenv( 'IXP_SETUP_ADMIN_PASSWORD' );
                    } else {
                        ${$attributes[ 'var' ]} = str()->random( 12 );
                    }

                } else {

                    ${$attributes[ 'var' ]} = ask( $attributes[ 'prompt' ] . ' [' . $attributes[ 'default' ] . '] ' );

                    if( !${$attributes[ 'var' ]} ) {
                        ${$attributes[ 'var' ]} = $attributes[ 'default' ];
                    }
                }
            }

            $data[ $setting ] = ${$attributes[ 'var' ]};

            if( $setting !== 'admin-password' || $this->option( 'echo-password' ) ) {
                $table[] = [ $setting, ${$attributes[ 'var' ]} ];
            }

        }

        $this->table( ['Setting', 'Value'], $table );

        $validator = \Validator::make($data, [
            'asn' => 'required|integer|between:1,4294967295',
            'ixp-name' => 'required|string',
            'ixp-legalname' => 'required|string',
            'ixp-shortname' => 'required|string',
            'admin-name' => 'required|string',
            'admin-username' => 'required|string',
            'admin-email' => 'required|email',
            'admin-password' => 'required|string|min:10',
            'ixp-phone' => 'required|string',
            'ixp-email' => 'required|email',
            'ixp-billing-phone' => 'required|string',
            'ixp-billing-email' => 'required|email',
            'ixp-url' => 'required|url',
        ]);

        if ( !$this->option('force') && $validator->fails()) {
            $this->error('The following errors occurred:');
            foreach ($validator->errors()->all() as $error) {
                $this->error("\t" . $error);
            }
            return 2;
        }

        if( !$this->option('force') && !$this->option( 'skip-confirm' ) ) {
            if( !$this->confirm( 'Is this information correct, and do you want to continue to create the database objects?' ) ) {
                $this->error( 'No confirmation was given. Exiting.' );
                return 3;
            }
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
                'billingContatName' => $ixpshortname . ' Billing Team',
                'billingEmail'      => $ixpbillingemail,
                'invoiceMethod'     => CompanyBillingDetail::INVOICE_METHOD_EMAIL,
                'billingFrequency'  => CompanyBillingDetail::BILLING_FREQUENCY_NOBILLING,
                'created_at'        => Carbon::now(),
                'updated_at'        => Carbon::now(),
            ] );

            $registrationDetail = CompanyRegisteredDetail::create( [
                'registeredName' => $ixplegalname,
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

    private function confirmDotEnv(): bool
    {
        $this->alert( "The following options are taken directly from the .env file:" );

        foreach( $this->ixpdata as $setting => $attributes ) {

            if( !isset( $attributes[ 'ask' ] ) || !$attributes[ 'ask' ] ) {
                $this->line( "\t{$attributes['env']}    =>    " . config( $attributes[ 'config' ] ) );
            }

        }

        $this->newLine(2);

        if( $this->option( 'force' ) ) {
            return true;
        }

        return $this->confirm( 'Do you want to continue?' );
    }


}