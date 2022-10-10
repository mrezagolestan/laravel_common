<?php

namespace Piod\LaravelCommon\BaseRepositories;


use Piod\LaravelCommon\UserRepository;
use Illuminate\Support\Facades\Log;
use Piod\LaravelCommon\VehicleDeviceRepository;

class EventGetBase
{

    //------------------------------------------ USER ------------------------------------------------
    public static function create_user($storedAt, $userId, $vehicleDeviceId, $payload)
    {
        UserRepository::get($userId);
    }

    public static function delete_user($storedAt, $userId, $vehicleDeviceId, $payload)
    {
        UserRepository::delete($userId);
    }


    public static function update_user($storedAt, $userId, $vehicleDeviceId, $payload)
    {
        UserRepository::reset($userId);
    }

    public static function logout_user($storedAt, $userId, $vehicleDeviceId, $payload)
    {
        UserRepository::reset($userId);
    }

    //------------------------------------------ Vehicle Device ------------------------------------------------

    public static function vehicle_device_activation($storedAt, $userId, $vehicleDeviceId, $payload)
    {
        VehicleDeviceRepository::get($vehicleDeviceId);
    }

    public static function vehicle_device_inactivation($storedAt, $userId, $vehicleDeviceId, $payload)
    {
        VehicleDeviceRepository::delete($vehicleDeviceId);
    }

    public static function vehicle_device_device_update($storedAt, $userId, $vehicleDeviceId, $payload)
    {
        // nothing to do
    }

    //------------------------------------------ User Car ------------------------------------------------

    public static function create_user_car($storedAt, $userId, $vehicleDeviceId, $payload)
    {
        // nothing to do
    }

    public static function delete_user_car($storedAt, $userId, $vehicleDeviceId, $payload)
    {
        VehicleDeviceRepository::delete($vehicleDeviceId);
    }

    public static function update_user_car($storedAt, $userId, $vehicleDeviceId, $payload)
    {
        VehicleDeviceRepository::reset($vehicleDeviceId);
    }

    //------------------------------------------ Vehicle Device Setting ------------------------------------------------

    public static function vehicle_device_setting_update($storedAt, $userId, $vehicleDeviceId, $payload)
    {
        // nothing to do
    }

    //------------------------------------------ VD config actions ------------------------------------------------


    public static function delete_faultcode($storedAt, $userId, $vehicleDeviceId, $payload)
    {
        // nothing to do
    }

    public static function manual_diag($storedAt, $userId, $vehicleDeviceId, $payload)
    {
        // nothing to do
    }

    public static function update_kill_switch_state($storedAt, $userId, $vehicleDeviceId, $payload)
    {
        // nothing to do
    }

    //------------------------------------------ Reminder ------------------------------------------------

    public static function reminder_create($storedAt, $userId, $vehicleDeviceId, $payload)
    {
        // nothing to do
    }

    public static function reminder_update($storedAt, $userId, $vehicleDeviceId, $payload)
    {
        // nothing to do
    }

    public static function reminder_delete($storedAt, $userId, $vehicleDeviceId, $payload)
    {
        // nothing to do
    }

    //------------------------------------------ Periodic Service ------------------------------------------------

    public static function periodic_service_init($storedAt, $userId, $vehicleDeviceId, $payload)
    {
        // nothing to do
    }

    public static function periodic_service_reset($storedAt, $userId, $vehicleDeviceId, $payload)
    {
        // nothing to do
    }


}