<?php

namespace App\Filament\Resources\EmployeeResource\Pages;

use App\Filament\Resources\EmployeeResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Filament\Notifications\Notification;

class EditEmployee extends EditRecord
{
    protected static string $resource = EmployeeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
            Actions\ForceDeleteAction::make(),
            Actions\RestoreAction::make(),
        ];
    }
    protected function getCreatedNotificationTitle(): ?string
    {
        return 'Employee Created Successfully';
    }

    protected function getCreatedNotificationDescription(): ?string
    {
        return 'The employee has been created successfully.';
    }
    protected function getCreatedNotificationMessage(): ?string
    {
        return 'The employee has been created successfully.';
    }
    protected function getCreatedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title($this->getCreatedNotificationTitle())
            ->description($this->getCreatedNotificationDescription())
            ->message($this->getCreatedNotificationMessage());
    }
}
