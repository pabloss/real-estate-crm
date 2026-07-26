# GEMINI.md

## 1. Project Overview

This project is a comprehensive portfolio piece demonstrating a full-stack Real Estate CRM (Customer Relationship Management) system. The application is designed to manage property listings and handle sales leads. It consists of a backend API, a frontend web application, and a containerized development environment. The architecture is heavily based on Domain-Driven Design (DDD) and Command Query Responsibility Segregation (CQRS) principles.

## 2. Tech Stack

*   **Backend**:
    *   PHP 8.1+
    *   Symfony 6+
    *   Doctrine ORM
    *   PostgreSQL
    *   Mercure (for real-time updates)
    *   LexikJWTAuthenticationBundle (for authentication)
*   **Frontend**:
    *   React 18+
    *   Vite
    *   Material-UI (MUI)
*   **Environment**:
    *   Docker & Docker Compose
    *   Nginx

## 3. Architecture

The project is structured as a monorepo with separate `backend`, `frontend`, and `docker` directories.

### 3.1. Backend Architecture

The backend is a Symfony application following Domain-Driven Design (DDD) principles and a modular architecture. The core business logic is organized into modules within `src/Modules`.

*   **Modules**: Each module represents a bounded context (e.g., `PropertyCatalog`, `SalesCRM`).
*   **Layers**: Inside each module, the code is structured into three layers:
    *   **Domain**: Contains the core business logic, including Entities, Value Objects, Domain Events, and Repository interfaces. This layer is independent of any framework.
    *   **Application**: Orchestrates the domain layer. It contains Commands and Queries (CQRS pattern) and their handlers.
    *   **Infrastructure**: Provides implementations for the interfaces defined in the domain layer. This includes Doctrine repositories, Symfony controllers, and other framework-specific code.
*   **CQRS (Command Query Responsibility Segregation)**: The application separates write operations (Commands) from read operations (Queries).
    *   **Commands** (`Application/Command`): Used to change the state of the application. They are dispatched on a command bus.
    *   **Queries** (`Application/Query`): Used to read data. They are handled by query handlers that often return Data Transfer Objects (DTOs).
*   **Persistence**: Doctrine ORM is used for data persistence. Mappings are defined in XML format (`Infrastructure/Persistence/Mapping`).

### 3.2. Frontend Architecture

The frontend is a single-page application (SPA) built with React and Vite.

*   **Components**: UI is built using React components located in `src/components`.
*   **Styling**: The application uses Material-UI for its component library and styling.
*   **API Communication**: The frontend communicates with the backend via a RESTful API. The Vite development server is configured to proxy requests to the backend API.

## 4. Development Environment

The entire development environment is managed by Docker Compose.

*   To start the environment, run: `docker-compose up -d`
*   **Services**:
    *   `php`: The Symfony application.
    *   `nginx`: Web server for the backend.
    *   `database`: PostgreSQL database.
    *   `frontend`: The React development server (Vite).
    *   `mercure`: Real-time push service.
*   **Access URLs**:
    *   Frontend: `http://localhost:5173`
    *   Backend API: `http://localhost:8080`
    *   Mercure Hub: `http://localhost:9090`

## 5. Coding Conventions

*   **Backend**:
    *   Follow Symfony and Doctrine best practices.
    *   Adhere to the DDD and CQRS patterns established in the existing modules.
    *   Entities should encapsulate business logic. State should be modified through methods on the entity, not public setters.
    *   Use the command and query buses for all state changes and data retrieval.
*   **Frontend**:
    *   Follow React best practices (e.g., hooks, functional components).
    *   Use Material-UI components for a consistent look and feel.

---