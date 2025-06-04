<?php

namespace App\Exports;

use App\Models\Meeting;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Illuminate\Support\Facades\DB;

class MeetingsExport implements FromCollection, WithHeadings, WithMapping
{
    protected $date = null;
    protected $issuerId = null;
    protected $roomId = null;
    protected $format = null;
    protected $search = null;
    protected $status = null;

    public function __construct($date = null, $issuerId = null, $roomId = null, $format = null, $search = null, $status = null)
    {
        $this->date = $date;
        $this->issuerId = $issuerId;
        $this->format = $format;
        $this->roomId = $roomId;
        $this->search = $search;
        $this->status = $status;
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        $query = Meeting::query()
                 ->with(['room', 'timeSlot', 'issuer', 'issuer.organization', 'investors'])
                 ->withCount(['investors', 'questions']);

        // Filter by date
        if ($this->date && $this->date !== 'all') {
            $query->whereHas('timeSlot', function($q) {
                $q->whereDate('date', $this->date);
            });
        }

        // Filter by issuer
        if ($this->issuerId && $this->issuerId !== 'all') {
            $query->where('issuer_id', $this->issuerId);
        }

        // Filter by room
        if ($this->roomId && $this->roomId !== 'all') {
            if ($this->roomId === 'null') {
                $query->whereNull('room_id');
            } else {
                $query->where('room_id', $this->roomId);
            }
        }

        // Filter by format (one-on-one or group)
        if ($this->format !== null && $this->format !== 'all') {
            $query->where('is_one_on_one', (bool) $this->format);
        }

        // Filter by status
        if ($this->status && $this->status !== 'all') {
            $query->where('status', $this->status);
        }

        // Search by issuer name, organization, notes, etc.
        if ($this->search) {
            $query->where(function ($q) {
                $q->where('notes', 'like', "%{$this->search}%")
                  ->orWhereHas('issuer', function ($subq) {
                      $subq->where('first_name', 'like', "%{$this->search}%")
                           ->orWhere('name', 'like', "%{$this->search}%")
                           ->orWhereHas('organization', function ($orgq) {
                                $orgq->where('name', 'like', "%{$this->search}%");
                           });
                  })
                  ->orWhereHas('room', function ($roomq) {
                        $roomq->where('name', 'like', "%{$this->search}%");
                  });
            });
        }

        // Tri par date et heure
        $query->join('time_slots', 'meetings.time_slot_id', '=', 'time_slots.id')
              ->orderBy('time_slots.date', 'asc')
              ->orderBy('time_slots.start_time', 'asc')
              ->select('meetings.*');

        return $query->get();
    }

    /**
     * @var Meeting $meeting
     */
    public function map($meeting): array
    {
        // Format investors names
        $investorNames = $meeting->investors->map(function($investor) {
            return $investor->first_name . ' ' . $investor->name;
        })->implode(', ');

        // Format meeting date and time
        $meetingDate = $meeting->timeSlot->date->format('Y-m-d');
        $meetingTime = $meeting->timeSlot->start_time->format('H:i') . ' - ' . $meeting->timeSlot->end_time->format('H:i');

        return [
            $meeting->id,
            $meetingDate,
            $meetingTime,
            $meeting->room ? $meeting->room->name : 'No Room',
            $meeting->room ? $meeting->room->location : 'N/A',
            $meeting->issuer->first_name . ' ' . $meeting->issuer->name,
            $meeting->issuer->organization->name ?? 'No Organization',
            $investorNames,
            $meeting->investors_count,
            $meeting->is_one_on_one ? 'One-on-One' : 'Group',
            $meeting->questions_count,
            $meeting->status->label(),
            $meeting->notes ?? '',
            $meeting->created_at->format('Y-m-d H:i:s'),
        ];
    }

    public function headings(): array
    {
        return [
            'ID',
            'Date',
            'Time',
            'Room',
            'Location',
            'Issuer',
            'Issuer Organization',
            'Investors',
            'Investors Count',
            'Meeting Format',
            'Questions Count',
            'Status',
            'Notes',
            'Created At',
        ];
    }
}
