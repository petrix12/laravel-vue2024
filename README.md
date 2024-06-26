# Taller de Laravel + Vue

## Creación del backend
1. Crear proyecto backend:
    ```bash
    laravel new speaksmarter
    ```
    :::tip Nota
    Seleccionar las opciones por defecto.
    :::
2. crear base de datos **speaksmarter**.
3. Configurar el archivo de variables de entorno **.env**:
    ```env title=".env"
    APP_NAME=Speaksmarter
    # ...
    APP_URL=http://speaksmarter.test
    # ...
    DB_CONNECTION=mysql
    DB_HOST=127.0.0.1
    DB_PORT=3306
    DB_DATABASE=speaksmarter
    DB_USERNAME=root
    DB_PASSWORD=    
    # ...
    ```

## Entidades principales
+ Módulo Usuarios y Roles:
    + USERS
    + ROLES
    + PERMMISIONS
+ Módulo Lecciones:
    + LESSONS
    + CATEGORY
    + LEVELS

## Modelar la estructura básica para el módulo de lecciones
1. Crear los modelos **Lesson**, **Category** y **Level**:
    ```bash
    php artisan make:model Level -m
    php artisan make:model Lesson -m
    php artisan make:model Category -m
    ```
2. Crear migraciones para tablas pivotes:
    ```bash
    php artisan make:migration create_category_lesson_table
    ```
3. Establecer los campos de la tabla **lessons**:
    ```php title="database\migrations\2024_06_26_051845_create_lessons_table.php"
    // ...
    public function up(): void
    {
        Schema::create('lessons', function (Blueprint $table) {
            $table->id();
            $table->string('name', 150);
            $table->text('description')->max(400);
            $table->string('image_uri', 255)->nullable();
            $table->string('content_uri', 255);
            $table->string('pdf_uri', 255);
            $table->unsignedBigInteger('level_id');
            $table->foreign('level_id')->references('id')->on('levels')->onUpdate('cascade')->onDelete('cascade');
            $table->timestamps();
        });
    }
    // ...
    ```
4. Establecer los campos de la tabla **categories**:
    ```php title="database\migrations\2024_06_26_052002_create_categories_table.php"
    // ...
    public function up(): void
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->timestamps();
        });
    }    
    // ...
    ```
5. Establecer los campos de la tabla **levels**:
    ```php title="database\migrations\2024_06_26_050146_create_levels_table.php"
    // ...
    public function up(): void
    {
        Schema::create('levels', function (Blueprint $table) {
            $table->id();
            $table->string('name', 2);
            $table->timestamps();
        });
    }    
    // ...
    ```
6. Establecer los campos de la tabla auxiliar **category_lesson**:
    ```php title="database\migrations\2024_06_26_052330_create_category_lesson_table.php"
    // ...
    public function up(): void
    {
        Schema::create('category_lesson', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('category_id');
            $table->unsignedInteger('lesson_id');
            $table->timestamps();
        });
    }    
    // ...
    ```
7. Configurar el modelo **Lesson**:
    ```php title="app\Models\Lesson.php"
    // ...
    class Lesson extends Model
    {
        // ...
        protected $guarded = [];

        public function categories(){
            return $this->belongsToMany(Category::class);
        }

        public function level() {
            return $this->belongsTo(Level::class);
        }
    }    
    ```
8. Configurar el modelo **Category**:
    ```php title="app\Models\Category.php"
    // ...
    class Level extends Model
    {
        // ...
        protected $guarded = [];

        public function lessons() {
            return $this->hasMany(Lesson::class);
        }
    }    
    ```
9. Configurar el modelo  **Level**:
    ```php title="app\Models\Level.php"
    // ...
    class Category extends Model
    {
        // ...
        protected $guarded = [];

        public function lessons() {
            return $this->belongsToMany(Lesson::class);
        }
    }    
    ```
10. Crear seeder para poblar la tabla **levels**:
    ```bash
    php artisan make:seeder LevelSeeder
    ```
11. Programar seeder **LevelSeeder**:
    ```php title="database\seeders\LevelSeeder.php"
    // ...
    use App\Models\Level;
    // ...
    class LevelSeeder extends Seeder
    {
        public function run(): void
        {
            Level::create(['name' => 'A1']);
            Level::create(['name' => 'A2']);
            Level::create(['name' => 'B1']);
            Level::create(['name' => 'B2']);
            Level::create(['name' => 'C1']);
            Level::create(['name' => 'C2']);
        }
    }    
    ```
12. Programar seeder principal **DatabaseSeeder**:
    ```php title="database\seeders\DatabaseSeeder.php"
    // ...
    public function run(): void
    {
        $this->call(LevelSeeder::class);
    }
    // ...
    ```
13. Ejecutar migraciones:
    ```bash
    php artisan migrate

    ```
14. mmm