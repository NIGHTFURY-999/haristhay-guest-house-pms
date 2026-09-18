<div align="center">

# Haristhay Guest House PMS

**A customized hotel and guest-house property management system**

</div>

---

## About

**Haristhay Guest House PMS** is a customized hotel/property management system being developed for guest-house operations.

The project is based on the open-source QloApps codebase and contains modifications, cleanup, and new development focused on the operational requirements of a small guest house.

The system is being developed with a **local-first architecture**, with permanent operational data intended to remain on the local system.

---

## Current Scope

The project is being developed around the following requirements:

- 🏨 Room and booking management
- 👤 Guest management
- 🪪 Guest registration and identity documentation
- 💱 Multicurrency support
- 🌐 Multilingual support
- 📊 Hotel reports and operational dashboards
- 💳 Payment functionality
- 🔐 Local data storage
- 🖥️ Local MySQL database

### Planned Features

The following features are planned or under development:

- ✅ Online guest check-in workflow
- 🤖 AI / ChatGPT guest assistant
- 🔄 Temporary encrypted synchronization queue for situations where the local PC is offline
- 📋 Guest-house specific operational dashboard
- 🔐 Additional security controls for guest documents

> Features marked as planned or under development should not be considered production-ready.

---

## Architecture

The intended architecture is primarily local:

```text
Guest
  │
  ▼
Haristhay Guest House PMS
  │
  ├── Booking Management
  ├── Guest Management
  ├── Room Management
  ├── Guest Registration
  ├── Online Check-in
  ├── Reports & Dashboard
  └── AI Assistant
          │
          ▼
      Local MySQL
          │
          ▼
   Local Guest Documents