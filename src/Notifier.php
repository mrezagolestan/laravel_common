<?php

namespace Piod\LaravelCommon;

class Notifier
{
    public const LEVEL_CRITICAL = 'critical';
    public const LEVEL_ERROR = 'error';
    public const LEVEL_WARNING = 'warning';
    public const LEVEL_INFO = 'info';


    protected string $type = '';
    protected ?int $organizationId = null;
    protected ?int $userId = null;
    protected ?int $vehicleDeviceId = null;
    protected bool $isDisplayable = false;
    protected bool $isPushNotified = false;
    protected bool $isSmsSent = false;
    protected string $text = '';
    protected null|string|int $voice = null;
    protected ?string $icon = null;
    protected string $level = 'info';
    protected array $actions = [];
    protected ?string $sn = null;
    protected $logData = null;


    public function type(string $value)
    {
        $this->type = $value;
    }

    public function organizationId(int $value)
    {
        $this->organizationId = $value;
    }

    public function userId(int $value)
    {
        $this->userId = $value;
    }

    public function vehicleDeviceId(int $value)
    {
        $this->vehicleDeviceId = $value;
    }

    public function display(bool $value)
    {
        $this->isDisplayable = $value;
    }

    public function pushNotify(bool $value)
    {
        $this->isPushNotified = $value;
    }

    public function smsSend(bool $value)
    {
        $this->isSmsSent = $value;
    }

    public function text(string $value)
    {
        $this->text = $value;
    }

    public function voice(string|int $value)
    {
        $this->voice = $value;
    }

    public function icon(string $value)
    {
        $this->icon = $value;
    }

    public function level(string $value)
    {
        $this->level = $value;
    }

    public function actions(array $value)
    {
        $this->actions = $value;
    }

    public function sn(string $value)
    {
        $this->sn = $value;
    }

    public function logData($value)
    {
        $this->logData = $value;
    }

    public function send()
    {
        $body = (object)[
            'type' => $this->type,
            'organization_id' => $this->organizationId,
            'user_id' => $this->userId,
            'vehicle_device_id' => $this->vehicleDeviceId,
            'is_displayable' => $this->isDisplayable,
            'is_push_notified' => $this->isPushNotified,
            'is_sms_sent' => $this->isSmsSent,
            'text' => $this->text,
            'voice' => $this->voice,
            'icon' => $this->icon,
            'level' => $this->level,
            'actions' => $this->actions,
            'sn' => $this->sn,
            'log_data' => $this->logData,
        ];
        $exchangeName = config('piod_common.notifier.'.$this->level . '_notifier_exchange_name');
        Rabbit::publish(config('notifier_publish_persistent'),$body,$exchangeName,'');
    }
}
