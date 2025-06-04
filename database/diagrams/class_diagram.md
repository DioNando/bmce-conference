# Diagramme de Classes Principal - Entités Métier

## Description

Ce diagramme présente les principales entités métier du système BMCE Invest et leurs relations directes. Il couvre la gestion des utilisateurs, organisations, rendez-vous et le système de permissions de base. Ce diagramme constitue le cœur fonctionnel de l'application et montre les interactions essentielles entre les différents composants du domaine métier.

```mermaid
classDiagram
    class User {
        +id: bigint
        +name: string
        +first_name: string
        +email: string
        +password: string
        +phone: string
        +position: string
        +organization_id: bigint
        +status: boolean
        +created_at: timestamp
        +updated_at: timestamp
        +organization()
        +issuerMeetings()
        +investorMeetings()
        +questions()
        +createdMeetings()
        +updatedMeetings()
        +hasRole()
        +timeSlots()
    }

    class Organization {
        +id: bigint
        +name: string
        +origin: enum[national, foreign]
        +profil: enum[issuer, investor]
        +organization_type: string
        +organization_type_other: string
        +logo: string
        +country_id: bigint
        +description: text
        +created_at: timestamp
        +updated_at: timestamp
        +users()
        +country()
    }

    class Country {
        +id: bigint
        +name_fr: string
        +name_en: string
        +code: string
        +created_at: timestamp
        +updated_at: timestamp
        +organizations()
    }

    class TimeSlot {
        +id: bigint
        +user_id: bigint
        +date: date
        +start_time: time
        +end_time: time
        +availability: boolean
        +created_at: timestamp
        +updated_at: timestamp
        +meetings()
        +user()
    }

    class Meeting {
        +id: bigint
        +room_id: bigint
        +time_slot_id: bigint
        +issuer_id: bigint
        +created_by_id: bigint
        +updated_by_id: bigint
        +status: enum[pending, confirmed, cancelled]
        +notes: text
        +is_one_on_one: boolean
        +created_at: timestamp
        +updated_at: timestamp
        +room()
        +timeSlot()
        +issuer()
        +investors()
        +createdBy()
        +updatedBy()
        +questions()
    }

    class MeetingInvestor {
        +id: bigint
        +meeting_id: bigint
        +investor_id: bigint
        +status: enum[pending, confirmed, declined]
        +created_at: timestamp
        +updated_at: timestamp
        +meeting()
        +investor()
    }

    class Question {
        +id: bigint
        +meeting_id: bigint
        +investor_id: bigint
        +question: text
        +is_answered: boolean
        +created_at: timestamp
        +updated_at: timestamp
        +meeting()
        +askedBy()
    }

    class Room {
        +id: bigint
        +name: string
        +capacity: integer
        +location: string
        +created_at: timestamp
        +updated_at: timestamp
        +meetings()
    }

    class Role {
        +id: bigint
        +name: string
        +guard_name: string
        +created_at: timestamp
        +updated_at: timestamp
        +permissions()
        +users()
    }

    class Permission {
        +id: bigint
        +name: string
        +guard_name: string
        +created_at: timestamp
        +updated_at: timestamp
        +roles()
    }

    User "1" -- "1" Organization : belongs to
    Organization "many" -- "1" Country : based in
    User "1" -- "many" TimeSlot : owns
    TimeSlot "1" -- "many" Meeting : provides time for
    Room "1" -- "many" Meeting : hosts
    User "1" -- "many" Meeting : participates as issuer
    User "1" -- "many" MeetingInvestor : participates as investor
    User "1" -- "many" Meeting : creates
    User "1" -- "many" Meeting : updates
    Meeting "1" -- "many" Question : has
    User "1" -- "many" Question : asks
    User "many" -- "many" Role : has
    Role "many" -- "many" Permission : has
    Meeting "1" -- "many" MeetingInvestor : has
```
