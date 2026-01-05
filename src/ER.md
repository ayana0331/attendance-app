```mermaid
erDiagram
    users ||--o{ attendances : "1対多"
    users ||--o{ attendance_corrections : "1対多"
    attendances ||--o{ breaks : "1対多"
    attendances |o--o| attendance_corrections : "1対1(任意)"

    users {
        bigint id PK
        string name
        string email UK
        string password
        boolean is_admin
    }

    admins {
        bigint id PK
        string email UK
        string password
    }

    attendances {
        bigint id PK
        bigint user_id FK
        date date
        time clock_in
        time clock_out
        integer total_minutes
    }

    breaks {
        bigint id PK
        bigint attendance_id FK
        time break_start
        time break_end
        integer duration
    }

    attendance_corrections {
        bigint id PK
        bigint user_id FK
        bigint attendance_id FK
        date date
        time clock_in
        time clock_out
        text reason
        string status
    }```