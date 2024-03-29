<?php
namespace IXP\Console\Commands;

define('strict_types', 1);

/*
 * Copyright (C) 2009 - 2024 Internet Neutral Exchange Association Company Limited By Guarantee.
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


use IXP\Models\{
    CompanyBillingDetail,
    CompanyRegisteredDetail,
    Customer,
    Infrastructure,
    User
};

/**
 * Artisan command to streamline the initial installation of IXP Manager
 *
 * @author     Iskren Hadzhinedev <i.hadzhinedev@gmail.com>
 * @package    IXP\Console\Commands
 * @copyright  Copyright (C) 2009 - 2024 Internet Neutral Exchange Association Company Limited By Guarantee
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
        . ' {--N|name= : The name of the admin user}'
        . ' {--U|username= : The username of the admin user}'
        . ' {--E|email= : The email of the admin user}'
        . ' {--A|asn= : The ASN of your IXP}'
        . ' {--I|infrastructure= : The name of your primary infrastructure}'
        . ' {--C|company-name= : The name of your company}';

    /**
     * The console command description.
     * @var string
     */
    protected $description = "Run initial setup for IXP Manager";

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

        $this->info('Starting the setup wizard...');
        $data = $this->populateData();

        $passhash = password_hash($data['password'], PASSWORD_BCRYPT, ['cost' => 10]);

        try {
            DB::transaction(function () use ($data, $passhash) {

                Infrastructure::create([
                    'name' => $data['infrastructure'],
                    'shortname' => $data['infrastructure'],
                    'isPrimary' => 1,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ]);

                $billingDetail = CompanyBillingDetail::create([
                    'billingContatName' => $data['name'],
                    'billingEmail' => config('identity.billing_email', $data['email']),
                    'invoiceMethod' => CompanyBillingDetail::INVOICE_METHOD_EMAIL,
                    'billingFrequency' => CompanyBillingDetail::BILLING_FREQUENCY_NOBILLING,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ]);

                $registrationDetail = CompanyRegisteredDetail::create([
                    'registeredName' => $data['company_name'],
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ]);

                $cust = Customer::create([
                    'name' => $data['company_name'],
                    'type' => 3,
                    'shortname' => $data['company_name'],
                    'autsys' => $data['asn'],
                    'maxprefixes' => 100,
                    'peeringemail' => $data['email'],
                    'peeringpolicy' => 'mandatory',
                    'nocphone' => config('identity.support_phone', '+1 555 555 5555'),
                    'noc24hphone' => config('identity.support_phone', '+1 555 555 5555'),
                    'nocemail' => config('identity.support_email', $data['email']),
                    'nochours' => config('identity.support_hours', '24/7'),
                    'nocwww' => config('app.url', 'http://example.com'),
                    'corpwww' => config('identity.corporate_url', 'http://example.com'),
                    'datejoin' => Carbon::now(),
                    'status' => 1,
                    'activepeeringmatrix' => 1,
                    'company_registered_detail_id' => $registrationDetail->id,
                    'company_billing_details_id' => $billingDetail->id,
                    'abbreviatedName' => $data['company_name'],
                    'isReseller' => 0,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ]);

                $cust->contacts()->create([
                    'name' => $data['name'],
                    'email' => $data['email'],
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ]);

                $user = new User;
                $user->name = $data['name'];
                $user->username = $data['username'];
                $user->password = $passhash;
                $user->email = $data['email'];
                $user->privs = User::AUTH_SUPERUSER;
                $user->disabled = 0;
                $user->creator = 'IXP Manager setup wizard';
                $user->created_at = Carbon::now();
                $user->updated_at = Carbon::now();

                $user->save();
                $user->customer()->associate($cust);
                $user->customers()->attach($cust->id);
                $user->currentCustomerToUser()->update(['privs' => User::AUTH_SUPERUSER]);
            });
        }
        catch (\Exception $e) {
            $this->error('A database error occurred while setting up IXP Manager:' . $e->getMessage());
            return 2;
        }

        return 0;
    }

    protected function populateData(): array
    {

        $data = [
            "asn" => $this->option('asn') ?? $this->ask('Enter the ASN of your IXP'),
            "company_name" => $this->option('company-name') ?? $this->ask('Enter the name of your company'),
            "infrastructure" => $this->option('infrastructure') ?? $this->ask('Enter the name of your primary infrastructure'),
            "name" => $this->option('name') ?? $this->ask('Enter the full name(s) of the admin user'),
            "username" => $this->option('username') ?? $this->ask('Enter the username of the admin user'),
            "email" => $this->option('email') ?? $this->ask('Enter the email of the admin user'),
            "password" => $this->secret('Enter the password of the admin user'),
        ];
        if ($data['password'] !== $this->secret('Confirm the password of the admin user')) {
            $this->error('Passwords do not match. Exiting.');
            exit(1);
        }

        $passwordRules = Password::min(8)
            ->mixedCase()
            ->numbers()
            ->symbols()
            ->uncompromised();
        $validator = \Validator::make($data, [
            'asn' => 'required|integer|between:1,4294967295',
            'company_name' => 'required|string',
            'infrastructure' => 'required|string',
            'name' => 'required|string',
            'username' => 'required|string',
            'email' => 'required|email',
            'password' => ['required', 'string', $passwordRules],
        ]);

        if ($validator->fails()) {
            $this->error('The following errors occurred:');
            foreach ($validator->errors()->all() as $error) {
                $this->error("\t" . $error);
            }
            exit(2);
        }
        return $data;

    }
}