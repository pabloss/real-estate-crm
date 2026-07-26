# Real Estate CRM

Kompleksowy system CRM (Customer Relationship Management) dla branży nieruchomości. Aplikacja służy do zarządzania ofertami nieruchomości oraz obsługi leadów sprzedażowych, stanowiąc rozbudowany projekt portfolio z zakresu architektury oprogramowania. Projekt bazuje na zasadach **Domain-Driven Design (DDD)** oraz **Command Query Responsibility Segregation (CQRS)**.

## 🛠 Technologie

Projekt składa się z backendowego API, aplikacji frontendowej oraz skonteneryzowanego środowiska.

### Backend
* PHP 8.1+
* Symfony 6+
* Doctrine ORM
* PostgreSQL
* Mercure (dla komunikacji w czasie rzeczywistym)
* LexikJWTAuthenticationBundle (uwierzytelnianie JWT)

### Frontend
* React 18+
* Vite
* Material-UI (MUI)

### Infrastruktura
* Docker & Docker Compose
* Nginx

---

## 🏗 Architektura

Projekt korzysta ze struktury monorepo podzielonej na katalogi: `backend`, `frontend` oraz `docker`.

### Architektura Backendowa (Symfony)

Aplikacja backendowa opiera się na modułowej architekturze w duchu **DDD**. Logika biznesowa zawarta jest w katalogu `src/Modules`.

* **Moduły:** Reprezentują wydzielone konteksty (Bounded Contexts), takie jak np. `PropertyCatalog` czy `SalesCRM`.
* **Warstwy wewnątrz modułu:**
  * **Domain (Domena):** Logika biznesowa niezależna od frameworka. Znajdują się tu Encje (Entities), Obiekty Wartości (Value Objects), Zdarzenia Domenowe (Domain Events) oraz interfejsy Repozytoriów.
  * **Application (Aplikacja):** Orkiestracja warstwy domenowej z użyciem wzorca CQRS (Komendy, Zapytania i ich Handlery).
  * **Infrastructure (Infrastruktura):** Implementacja interfejsów (np. Repozytoria Doctrine), kontrolery Symfony, konfiguracja frameworka.
* **CQRS:** 
  * _Komendy_ zmieniają stan systemu (obsługiwane przez Command Bus).
  * _Zapytania_ służą tylko do odczytu danych (najczęściej zwracają obiekty DTO).
* **Baza danych:** Używany jest Doctrine ORM z mapowaniami definiowanymi poprzez pliki XML (`Infrastructure/Persistence/Mapping`).

### Architektura Frontendowa (React)

Frontend to nowoczesna aplikacja Single-Page Application (SPA).

* Komponenty UI zgrupowane są w `src/components`.
* Spójny wygląd i łatwe zarządzanie interfejsem zapewnia biblioteka komponentów **Material-UI**.
* Zarządzaniem cyklem budowania i lokalnym serwerem deweloperskim (z wbudowanym proxy do API) zajmuje się **Vite**.

---

## 🚀 Uruchomienie lokalnie (Docker)

Całe środowisko uruchomieniowe znajduje się w Docker Compose, co ułatwia start i rozwój aplikacji bez konieczności instalowania zależności bezpośrednio w systemie operacyjnym.

1. Sklonuj repozytorium.
2. W głównym katalogu projektu uruchom kontenery w tle:

```bash
docker-compose up -d
```

### Usługi i porty
Po pomyślnym uruchomieniu, poszczególne elementy aplikacji będą dostępne pod następującymi adresami:
* **Aplikacja Frontend (Vite):** `http://localhost:5173`
* **Backend API (Nginx):** `http://localhost:8080`
* **Mercure Hub:** `http://localhost:9090`

Kontenery w środowisku: `php` (Symfony), `nginx` (serwer WWW dla backendu), `database` (PostgreSQL), `frontend` (Vite) oraz `mercure` (obsługa web socket / push).

---

## 💡 Konwencje programistyczne

* **Backend:**
  * Kod powinien być zgodny z dobrymi praktykami Symfony i Doctrine.
  * Encje domenowe muszą enkapsulować logikę i dbać o poprawność swojego stanu wewnątrz klas (zmiana stanu odbywa się poprzez wyspecjalizowane metody encji, bez użycia publicznych setterów).
  * Komunikacja i akcje wpływające na stan są realizowane przez Command/Query Bus.
* **Frontend:**
  * Aplikacja React pisana jest w oparciu o komponenty funkcyjne i Hooki.
  * Interfejs użytkownika należy budować głównie w oparciu o dostarczone rozwiązania z Material-UI (MUI).
