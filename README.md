# Taller de Laravel + Vue

## Creación del backend
1. Crear proyecto backend:
    ```bash
    laravel new speaksmarter
    ```
    :::tip Nota
    Seleccionar las opciones por defecto.
    :::
2. Crear base de datos **speaksmarter**.
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
4. Instalar **Jetstream** e **Inertia**:
    ```bash
    composer require laravel/jetstream
    php artisan jetstream:install inertia
    ```
5. Instalar sistema de roles y permisos con **Laravel-Spatie**:
    ```bash
    composer require spatie/laravel-permission
    ```
6. Publicar las migraciones de **Laravel-Spatie** y el archivo de configuración **permission**:
    ```bash
    php artisan vendor:publish --provider="Spatie\Permission\PermissionServiceProvider"
    ```
7. Agregar el trade **HasRoles** al modelo **User**:
    ```php title="app\Models\User.php"
    // ...
    use Spatie\Permission\Traits\HasRoles;
    // ...
    class User extends Authenticatable
    {
        // ...
        use HasRoles;
        // ...
    }
    ```
8. Ejecutar las migraciones:
    ```bash
    php artisan migrate
    ```


## Entidades principales
+ Módulo Usuarios, Roles y Permisos:
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
12. Ejecutar migraciones:
    ```bash
    php artisan migrate
    ```

## Modelar la estructura básica para el módulo de usuarios, roles y permisos
1. Crear seeders **RoleSeeder** y **UserSeeder**:
    ```bash
    php artisan make:seeder RoleSeeder
    php artisan make:seeder UserSeeder
    ```
2. Programar seeder **RoleSeeder**:
    ```php title="database\seeders\RoleSeeder.php"
    // ...
    use Spatie\Permission\Models\Permission;
    use Spatie\Permission\Models\Role;
    // ...
    class RoleSeeder extends Seeder
    {
        // ...
        public function run(): void
        {
            $role_admin = Role::create(['name' => 'admin']);
            $role_editor = Role::create(['name' => 'editor']);

            $permission_create_role = Permission::create(['name' => 'create roles']);
            $permission_read_role = Permission::create(['name' => 'read roles']);
            $permission_update_role = Permission::create(['name' => 'update roles']);
            $permission_delete_role = Permission::create(['name' => 'delete roles']);

            $permission_create_lesson = Permission::create(['name' => 'create lessons']);
            $permission_read_lesson = Permission::create(['name' => 'read lessons']);
            $permission_update_lesson = Permission::create(['name' => 'update lessons']);
            $permission_delete_lesson = Permission::create(['name' => 'delete lessons']);

            $permission_create_category = Permission::create(['name' => 'create categories']);
            $permission_read_category = Permission::create(['name' => 'read categories']);
            $permission_update_category = Permission::create(['name' => 'update categories']);
            $permission_delete_category = Permission::create(['name' => 'delete categories']);

            $permissions_admin = [
                $permission_create_role,
                $permission_read_role,
                $permission_update_role,
                $permission_delete_role,
                $permission_create_lesson,
                $permission_read_lesson,
                $permission_update_lesson,
                $permission_delete_lesson,
                $permission_create_category,
                $permission_read_category,
                $permission_update_category,
                $permission_delete_category
            ];
            $permissions_editor = [
                $permission_create_lesson,
                $permission_read_lesson,
                $permission_update_lesson,
                $permission_delete_lesson,
                $permission_create_category,
                $permission_read_category,
                $permission_update_category,
                $permission_delete_category
            ];

            $role_admin->syncPermissions($permissions_admin);
            $role_editor->syncPermissions($permissions_editor);
        }
    }
    ```
3. Programar seeder **UserSeeder**:
    ```php title="database\seeders\UserSeeder.php"
    // ...
    use App\Models\User;
    use Illuminate\Support\Facades\Hash;
    // ...

    class UserSeeder extends Seeder
    {
        // ...
        public function run(): void
        {
            $admin = User::create([
                'name' => 'admin',
                'email' => 'admin@speaksmarter.net',
                'password' => Hash::make('admin'),
            ])->assignRole('admin');

            $editor = User::create([
                'name' => 'editor',
                'email' => 'editor@speaksmarter.net',
                'password' => Hash::make('editor'),
            ])->assignRole('editor');
        }
    }
    ```
4. Modificar el seeder principal **DatabaseSeeder** para incluir los seeders **LevelSeeder** (Creado en el modelado de lecciones), **RoleSeeder** y **UserSeeder**:
    ```php title="database\seeders\DatabaseSeeder.php"
    public function run(): void
    {
        $this->call([
            LevelSeeder::class,
            RoleSeeder::class,
            UserSeeder::class
        ]);
    }
    ```
5. Poner a disposición de los componentes vue de inertia los roles y permisos modificando el middleware **HandleInertiaRequests** para pasar por props la información indicada a los componentes:
    ```php title="app\Http\Middleware\HandleInertiaRequests.php"
    // ...
    public function share(Request $request): array
    {
        return array_merge(parent::share($request), [
            'user.roles' => $request->user() ? $request->user()->roles()->pluck('name') : [],
            'user.permissions' => $request->user() ? $request->user()->getPermissionsViaRoles()->pluck('name') : []
        ]);
    }
    // ...
    ```
    :::tip Nota
    + Para acceder a los props desde cualquier vista de inertia:
    ```html
    {{ $page.props }}
    ```
    + Para restringir un bloque de código html según el rol:
    ```html
    <button v-if="$page.props.user.permissions.includes('nombre del permiso')">Crear rol</button>
    ```
    :::
6. Ejecutar los seeder:
    ```bash
    php artisan db:seed
    ```
7. Instalar las dependencias npm:
    ```bash
    npm i
    npm run dev
    ```

