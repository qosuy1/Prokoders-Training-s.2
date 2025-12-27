# Prokoders Tasks - Management System

A comprehensive Laravel API application for managing Authors, Posts, Categories, and Comments with complete authorization and validation.

## 🚀 Features Implemented

### 1. **Author Management**

-   `StoreAuthorRequest` - Validation for creating new authors
-   `UpdateAuthorRequest` - Validation for updating author information
-   `AuthorPolicy` - Authorization checks for author operations
-   `AuthorService` - Business logic for author creation and updates
-   Full CRUD API endpoints for author management

### 2. **Post Management**

-   `CreatePostsRequest` - Validation for creating posts
-   `UpdatePostsRequest` - Validation for updating posts
-   `PostPolicy` - Authorization checks for post operations
-   Complete post lifecycle management through API

### 3. **Category Management**

-   `Category` Model with proper relationships
-   Full support for post-category associations
-   Category filtering and organization

### 4. **Comment Management**

-   `Comment` Model with complete relationships
-   `CommentPolicy` - Authorization for comment operations
-   Nested comment support on posts

## 🏗️ Architecture

### Models

-   **Author** - Represents content creators
-   **Post** - Represents articles/content
-   **Category** - Represents post categories
-   **Comment** - Represents post comments
-   **User** - User authentication and management

### Request Validation Classes

-   `StoreAuthorRequest` - Author creation validation
-   `UpdateAuthorRequest` - Author update validation
-   `CreatePostsRequest` - Post creation validation
-   `UpdatePostsRequest` - Post update validation

### Authorization Policies

-   `AuthorPolicy` - Controls author access
-   `PostPolicy` - Controls post access
-   `CommentPolicy` - Controls comment access

### Services

-   `AuthorService` - Handles author business logic

### Database

-   5 Main migrations for users, authors, posts, categories, and comments
-   `UserFactory` for test data generation
-   Database seeding with initial roles and test user

## 📊 Database Schema

```
users → authors (one-to-many)
users → posts (one-to-many)
users → comments (one-to-many)
posts → comments (one-to-many)
posts ↔ categories (many-to-many)
```

## 🔌 API Endpoints

### Authentication Endpoints

-   `POST /api/v1/register` - Register a new user
-   `POST /api/v1/login` - Login with email and password
-   `POST /api/v1/logout` - Logout (requires auth token)
-   `GET /api/v1/user` or `/api/v1/profile` - Get authenticated user profile (requires auth token)

### Author Endpoints

All endpoints follow RESTful conventions and require authentication:

-   `GET/POST /api/v1/authors`
-   `GET/PUT/DELETE /api/v1/authors/{id}`

### Post Endpoints

-   `GET/POST /api/v1/posts`
-   `GET/PUT/DELETE /api/v1/posts/{id}`
-   `POST /api/v1/posts/{post}/publishPost` - Publish a post

### Category Endpoints

-   `GET/POST /api/v1/categories`
-   `GET/PUT/DELETE /api/v1/categories/{id}`

### Comment Endpoints

-   `GET /api/v1/posts/{post}/comments` - Get post comments
-   `POST /api/v1/posts/{post}/comments` - Create comment (requires auth)
-   `PUT /api/v1/posts/{post}/comments/{comment}` - Update comment (requires auth)
-   `DELETE /api/v1/posts/{post}/comments/{comment}` - Delete comment (requires auth)

## 🔐 Authentication

The application uses **Laravel Sanctum** for API authentication:

### Register a New User

```bash
POST /api/v1/register
Content-Type: application/json

{
  "name": "John Doe",
  "email": "john@example.com",
  "password": "password",
  "password_confirmation": "password"
}
```

### Login

```bash
POST /api/v1/login
Content-Type: application/json

{
  "email": "john@example.com",
  "password": "password"
}
```

Response includes an access token to be used in subsequent requests:

```json
{
    "data": {
        "token": "your_access_token_here",
        "name": "John Doe",
        "email": "john@example.com",
        "user_role": "user"
    }
}
```

### Get User Profile

```bash
GET /api/v1/user
Authorization: Bearer {token}
```

### Logout

```bash
POST /api/v1/logout
Authorization: Bearer {token}
```

## ⚙️ Configuration

-   **Policy Bindings** - Configured in `AppServiceProvider`
-   **Rate Limiting** - Applied to API routes
-   **Middleware** - Authorization and validation applied globally
-   **Authentication** - Laravel Sanctum for API token authentication

## 🧪 Testing & Seeding

-   Database can be reset and seeded with: `php artisan migrate:fresh --seed`
-   `UserFactory` generates realistic test data
-   Ready for feature and unit testing

## 📝 How to Use

1. **Create an Author:**

    - Send POST request to `/api/v1/authors` with validated data
    - Uses `StoreAuthorRequest` validation

2. **Create a Post:**

    - Send POST request to `/api/v1/posts` with validated data
    - Attach categories through the pivot table

3. **Manage Comments:**

    - Comments can be created on any post
    - Authorization is checked through `CommentPolicy`

4. **Authorization:**
    - All endpoints check user permissions through policies
    - Users can only manage their own content (by default)

## 📦 Installation & Setup

```bash
# Install dependencies
composer install

# Copy environment file
cp .env.example .env

# Generate application key
php artisan key:generate

# Run migrations and seed database
php artisan migrate:fresh --seed

# Start the development server
php artisan serve
```

## 🔐 License

This project is licensed under the MIT License.
