# CoSE IT Tools Portal

The CoSE IT Tools Portal is a Laravel-based web application designed to centralise all your team’s tools in one place. With customisable groups, permissions, and metadata, it makes managing tools efficient and organised. Additionally, it features a news section to keep your team informed about the latest developments in your industry.

---

## Features

### Tools Management:
- Add, edit, and delete tools.
- Categorise tools with custom groups.
- Assign group-level permissions for better access control.

### User Management:
- Add and edit users with group assignments.
- Assign administrative privileges to selected users.

### Tech News Integration:
- Display the latest tech news from configurable sources and keywords.
- Uses the NewsAPI for fetching articles.

### Custom Metadata:
- Add descriptions, images, and colours to tools for better organisation and visibility.

---

## Installation

### Prerequisites
- PHP 8.2+
- Composer
- MySQL or MariaDB database
- Web server (Apache or Nginx)
- Write permissions for `storage/` and `bootstrap/cache/` directories.

---

### Steps

1. **Clone the repository**

   Run the following commands:

        git clone https://github.com/russsseeelll/cosetools.git cd cosetools


2. **Install dependencies**

    Use Composer to install the required PHP dependencies:

        composer install


3. **Copy the `.env.example` file to `.env`**

    Create a new `.env` file for your environment variables:

        cp .env.example .env


4. **Configure the `.env` file**

    Update the following database configuration values:

        DB_CONNECTION=mysql DB_HOST=127.0.0.1 DB_PORT=3306 DB_DATABASE=cosetools DB_USERNAME=root DB_PASSWORD=yourpassword


5. **Generate the application key**

    Run the following command to generate an application key:

        php artisan key:generate


6. **Run migrations to set up the database**

    Execute the database migrations:

        php artisan migrate


7. **Set permissions for writable directories**

    Ensure `storage` and `bootstrap/cache` directories are writable:

        chmod -R 775 storage bootstrap/cache chown -R www-data:www-data storage bootstrap/cache


8. **Serve the application**

    Start the Laravel development server:

        php artisan serve


By default, the application will be available at `http://localhost:8000`.

---

## Configuring the News Section

The news feature allows you to display the latest tech news from selected sources using the NewsAPI.

### Steps to Configure

1. **Enable the News Section**

    In the `.env` file, set `NEWS_ENABLED` to `true`:

        NEWS_ENABLED=true


2. **Add Your NewsAPI Key**

    Obtain an API key from NewsAPI and add it to the `.env` file:

        NEWS_API_KEY=your_news_api_key


3. **Set the News API URL**

    Use the default endpoint for NewsAPI:

        NEWS_API_URL=https://newsapi.org/v2/everything


4. **Configure Keywords and Domains**

    Define the keywords and domains for filtering news articles. Example configuration:

        NEWS_KEYWORDS="education technology,edtech,AI in education,robotics in classrooms,cybersecurity,cloud computing" 
        NEWS_DOMAINS=techcrunch.com,thenextweb.com,wired.com,arstechnica.com,theverge.com


5. **Clear and Cache Config**

    Run the following commands to ensure Laravel picks up the updated environment variables:

        php artisan config:clear php artisan config:cache


6. **Fetch News**

    Use the artisan command to fetch the latest news and populate the database:

        php artisan tech-news:fetch


---

## Usage

### Admin Features

Admins have full control over the tools and users in the portal, including:

- **Managing Tools**:
  - Add new tools with names, URLs, descriptions, images, and group permissions.
  - Edit existing tools to update details or change group assignments.
  - Delete tools that are no longer needed.
  - Customise tool visibility and order on the dashboard.

- **Managing Users**:
  - Add users with group assignments and optional admin privileges.
  - Edit user details such as their assigned groups or admin status.
  - Remove users when they no longer need access.

### Regular User View

- Regular users only see tools assigned to their groups.
- The tools are displayed as clickable cards with their name, description, and image.
- Users cannot see or manage other users or their tools. Users can change the order and visibility of tools assigned to them using the modal.

### News Section

- If enabled (`NEWS_ENABLED=true` in the `.env` file):
  - Displays a sidebar with the latest tech news based on keywords and sources.
  - News articles are fetched via the [NewsAPI](https://newsapi.org/).
  - Users can click on news headlines to read the full article in a new tab.



---

## License

This project is open-source and available under the MIT License.
