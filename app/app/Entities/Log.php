<?php

namespace App\Entities;

use App\ValueObjects\MailProvider;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Log
 * @property int id
 * @property string to
 * @property string body
 * @property array email_metadata
 * @property string provider_name
 * @property int|string provider
 * @property string failed_reason
 * @property Carbon sent_at
 * @property Carbon failed_at
 * @package App\Entities
 */
class Log extends Model
{
    /**
     * @var bool
     */
    public $timestamps = false;

    /**
     * @var array
     */
    private $providers = [];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'to', 'body', 'email_metadata', 'provider', 'failed_reason','sent_at', 'failed_at'
    ];

    /**
     * @var array
     */
    protected $dates = [
        'sent_at', 'failed_at'
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_metadata' => 'json',
    ];

    /**
     * @var array
     */
    protected $hidden = [
        'provider'
    ];

    /**
     * @var array
     */
    protected $appends = [
        'provider_name'
    ];

    /**
     * @return string
     */
    public function getProviderNameAttribute()
    {
        $providers = $this->getEmailProviders();

        return !empty($providers[$this->provider]) ? $providers[$this->provider] : MailProvider::NO_PROVIDERS;
    }

    /**
     * @return array
     * It will return an array like this
     * [ 'provider_id' => 'provider_name'] e.g: [ 2 => 'mailtrap', -1 => 'no_providers' ]
     */
    private function getEmailProviders(): array
    {
        if (!empty($this->providers)) {
            return $this->providers;
        }
        $providers = config('mail.providers');
        $result = [];
        foreach ($providers as $value) {
            $result[$value['id']] = $value['name'];
        }
        $result[MailProvider::NO_PROVIDERS] = 'No Provider';

        return $result;
    }
}
