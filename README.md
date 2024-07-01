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
    npm i @inertiajs/inertia
    ```
5. Instalar sistema de roles y permisos con **Laravel-Spatie**:
    ```bash
    composer require spatie/laravel-permission
    ```
    :::tip Documentación
    SPATIE: https://spatie.be/docs/laravel-permission/v6/installation-laravel
    :::
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
            $table->boolean('is_free')->default(false);
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

## Panel de administración
1. Crear controlador **DashboardController**:
    ```bash
    php artisan make:controller DashboardController
    ```
2. Programar controlador **DashboardController**:
    ```php title="app\Http\Controllers\DashboardController.php"
    // ...
    use Illuminate\Foundation\Application;
    use Illuminate\Support\Facades\Route;
    use Inertia\Inertia;

    class DashboardController extends Controller
    {
        public function index() {
            return Inertia::render('Welcome', [
                'canLogin' => Route::has('login'),
                'canRegister' => Route::has('register'),
                'laravelVersion' => Application::VERSION,
                'phpVersion' => PHP_VERSION,
            ]);
        }

        public function dashboard() {
            return Inertia::render('Dashboard');
        }
    }
    ```
3. Crear los controladores **CategoryController**, **LessonController** y **RoleController** con todos sus recursos:
    ```bash
    php artisan make:controller CategoryController -r
    php artisan make:controller LessonController -r
    php artisan make:controller RoleController -r
    ```
4. Crear custom request **CategoryRequest**:
    ```bash
    php artisan make:request CategoryRequest
    ```
5. Programar custom request **CategoryRequest**:
    ```php title="app\Http\Requests\CategoryRequest.php"
    // ...
    use Illuminate\Validation\Rule;

    class CategoryRequest extends FormRequest
    {
        // ...
        public function authorize(): bool
        {
            return true;
        }
        // ...
        public function rules(): array
        {
            return [
                'name' => ['required', 'string', 'max:100', Rule::unique(table: 'categories', column: 'name')->ignore(id: request('category'), idColumn: 'id')]
            ];
        }

        public function messages(): array
        {
            return [
                'name.unique' => __('Category already exists'),
            ];
        }
    }
    ```
6. Programar controlador **CategoryController**:
    ```php title="app\Http\Controllers\CategoryController.php"
    // ...
    use App\Http\Requests\CategoryRequest;
    use App\Models\Category;
    use Inertia\Ssr\Response;

    class CategoryController extends Controller
    { 
        const NOMBER_OF_ITEMS_PER_PAGE = 25;

        public function index()
        {
            //define('NOMBER_OF_ITEMS_PER_PAGE', 25);
            $categories = Category::paginate(self::NOMBER_OF_ITEMS_PER_PAGE);
            return inertia('Categories/Index', compact('categories'));
        }
        
        public function create()
        {
            return inertia('Categories/Create');
        }

        public function store(CategoryRequest $request)
        {
            Category::create($request->validated());
            return redirect()->route('categories.index');
        }    
        
        public function show(string $id) {}

        public function edit(Category $category)
        {
            return inertia('Categories/Edit', compact('category'));
        }
        
        public function update(CategoryRequest $request, Category $category)
        {
            $category->update($request->validated());
            return redirect()->route('categories.index');
        }
        
        public function destroy(Category $category)
        {
            $category->delete();
            return redirect()->route('categories.index');
        }
    }
    ```
7. Programar controlador **LessonController**:
    ```php title="app\Http\Controllers\LessonController.php"
    ```
8. Programar controlador **RoleController**:
    ```php title="app\Http\Controllers\RoleController.php"
    ```
9.  Establecer las rutas administrativas en el archivo de rutas **web**:
    ```php title="routes\web.php"
    <?php

    use App\Http\Controllers\DashboardController;
    use Illuminate\Support\Facades\Route;

    // Rutas no autenticadas
    Route::get('/', [DashboardController::class, 'index']);

    Route::middleware([
        'auth:sanctum',
        config('jetstream.auth_session'),
        'verified',
    ])->group(function () {
        // Rutas autenticadas
        Route::get('/dashboard', [DashboardController::class, 'dashboard'])->name('dashboard');
        Route::resource('/categories', \App\Http\Controllers\CategoryController::class);
        Route::resource('/lessons', \App\Http\Controllers\LessonController::class);
        Route::resource('/roles', \App\Http\Controllers\RoleController::class);
    });
    ```
10. Adaptar la plantilla **AppLayout** a nuestra aplicación:
    ```php title="resources\js\Layouts\AppLayout.vue"
    <!-- ... -->
    <!-- Navigation Links -->
    <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
        <NavLink :href="route('dashboard')" :active="route().current('dashboard')">
            Dashboard
        </NavLink>
    </div>
    <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex" v-if="$page.props.user.permissions.includes('read categories')">
        <NavLink :href="route('categories.index')" :active="route().current('categories.*')">
            Categories
        </NavLink>
    </div>
    <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex" v-if="$page.props.user.permissions.includes('read lessons')">
        <NavLink :href="route('lessons.index')" :active="route().current('lessons.*')">
            Lessons
        </NavLink>
    </div>
    <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex" v-if="$page.props.user.permissions.includes('read roles')">
        <NavLink :href="route('roles.index')" :active="route().current('roles.*')">
            Roles
        </NavLink>
    </div>
    <!-- ... -->
    <!-- Responsive Navigation Menu -->
    <!-- ... -->
    <div class="pt-2 pb-3 space-y-1">
        <ResponsiveNavLink :href="route('dashboard')" :active="route().current('dashboard')">
            Dashboard
        </ResponsiveNavLink>
    </div>
    <div class="pt-2 pb-3 space-y-1">
        <ResponsiveNavLink :href="route('categories.index')" :active="route().current('categories.*')" v-if="$page.props.user.permissions.includes('read categories')">
            Categories
        </ResponsiveNavLink>
    </div>
    <div class="pt-2 pb-3 space-y-1">
        <ResponsiveNavLink :href="route('lessons.index')" :active="route().current('lessons.*')" v-if="$page.props.user.permissions.includes('read lessons')">
            Lessons
        </ResponsiveNavLink>
    </div>
    <div class="pt-2 pb-3 space-y-1">
        <ResponsiveNavLink :href="route('roles.index')" :active="route().current('roles.*')" v-if="$page.props.user.permissions.includes('read roles')">
            Roles
        </ResponsiveNavLink>
    </div>
    <!-- ... -->
    ```
11. Diseñar las vistas para las categorias:
    1. Crear vista **Categories/Index**:
        ```html title="resources\js\Pages\Categories\Index.vue"
        <script>
        export default {
            name: 'CategoriesIndex'
        }
        </script>

        <script setup>
        import AppLayout from '@/Layouts/AppLayout.vue'
        import { Link } from '@inertiajs/vue3'
        import { Inertia } from '@inertiajs/inertia'

        defineProps({
            categories: {
                type: Object,
                required: true
            }
        })

        const deleteCategory = (id) => {
            if(confirm('Are you sure?')) {
                Inertia.delete(route('categories.destroy', id))
            }
        }
        </script>

        <template>
            <AppLayout title="Index category">
                <template #header>
                    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                        Categories
                    </h2>
                </template>
                <div class="py-12 ">
                    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                        <div                 
                            v-if="$page.props.user.permissions.includes('create categories')"
                            class="p-6 bg-white border-b border-gray-200"
                        >
                            <div class="flex justify-between">
                                <Link 
                                    :href="route('categories.create')" 
                                    class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded"
                                >
                                    Create category
                                </Link>
                            </div>
                        </div>
                        <div class="mt-4">
                            <div class="flex flex-col">
                                <div class="overflow-x-auto sm:mx-0.5 lg:mx-0.5">
                                    <div class="py-2 inline-block min-w-full sm:px-6 lg:px-8">
                                        <div class="overflow-hidden">
                                            <table class="min-w-full">
                                                <thead class="bg-white border-b">
                                                    <tr>
                                                    <th scope="col" class="text-sm font-medium text-gray-900 px-6 py-4 text-left">
                                                        #
                                                    </th>
                                                    <th scope="col" class="text-sm font-medium text-gray-900 px-6 py-4 text-left">
                                                        Name
                                                    </th>
                                                    <th scope="col" class="text-sm font-medium text-gray-900 px-6 py-4 text-left">
                                                        Edit
                                                    </th>
                                                    <th scope="col" class="text-sm font-medium text-gray-900 px-6 py-4 text-left">
                                                        Delete
                                                    </th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr class="bg-gray-100 border-b" v-for="category in categories.data" :key="category.id">
                                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                                            {{ category.id }}
                                                        </td>
                                                        <td class="text-sm text-gray-900 font-light px-6 py-4 whitespace-nowrap">
                                                            {{ category.name }}
                                                        </td>
                                                        <td class="text-sm text-gray-900 font-light px-6 py-4 whitespace-nowrap">
                                                            <Link 
                                                                v-if="$page.props.user.permissions.includes('update categories')"
                                                                :href="route('categories.edit', category.id)" 
                                                                class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded"
                                                            >
                                                                Edit
                                                            </Link>
                                                        </td>
                                                        <td class="text-sm text-gray-900 font-light px-6 py-4 whitespace-nowrap">
                                                            <Link 
                                                                v-if="$page.props.user.permissions.includes('delete categories')"
                                                                @click="deleteCategory(category.id)" 
                                                                class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded"
                                                            >
                                                                Delete
                                                            </Link>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="p-6 bg-white border-b border-gray-200">
                            <div class="flex justify-between mt-2">
                                <Link 
                                    v-if="categories.current_page > 1"
                                    :href="categories.prev_page_url" 
                                    class="py-2 px-4 rounded"
                                >
                                    PREV
                                </Link>
                                <div v-else></div>
                                <Link 
                                    v-if="categories.current_page < categories.last_page"
                                    :href="categories.next_page_url" 
                                    class="py-2 px-4 rounded"
                                >
                                    NEXT
                                </Link>
                                <div v-else></div>
                            </div>
                        </div>
                    </div>
                </div>
            </AppLayout>
        </template>
        ```
    2. Crear vista **Categories/Create**:
        ```html title="resources\js\Pages\Categories\Create.vue"
        <script>
        export default {
            name: 'CategoriesCreate'
        }
        </script>

        <script setup>
        import AppLayout from '@/Layouts/AppLayout.vue'
        import { useForm } from '@inertiajs/vue3'
        import CategoryForm from '@/Components/Categories/Form.vue'

        const form = useForm({
            name: ''
        })
        </script>

        <template>
            <AppLayout title="Create category">
                <template #header>
                    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                        Create category
                    </h2>
                </template>
                <div class="py-12 ">
                    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                        <div class="p-6 bg-white border-b border-gray-200">
                            <CategoryForm 
                                :form="form" 
                                @submit="form.post(route('categories.store'))"
                            />
                        </div>
                    </div>
                </div>        
            </AppLayout>
        </template>
        ```
    3. Crear vista **Categories/Edit**:
        ```html title="resources\js\Pages\Categories\Edit.vue"
        <script>
        export default {
            name: 'CategoriesEdit'
        }
        </script>

        <script setup>
        import AppLayout from '@/Layouts/AppLayout.vue'
        import { useForm } from '@inertiajs/vue3'
        import CategoryForm from '@/Components/Categories/Form.vue'

        const props = defineProps({
            category: {
                type: Object,
                required: true
            }
        })

        const form = useForm({
            name: props.category.name
        })
        </script>

        <template>
            <AppLayout title="Edit category">
                <template #header>
                    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                        Edit category
                    </h2>
                </template>
                <div class="py-12 ">
                    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                        <div class="p-6 bg-white border-b border-gray-200">
                            <CategoryForm 
                                :updating="true" 
                                :form="form" 
                                @submit="form.put(route('categories.update', category.id))"
                            />
                        </div>
                    </div>
                </div>        
            </AppLayout>
        </template>
        ```
    4. Crear componente **Components/Categories/Form.vue**:
        ```html title="resources\js\Components\Categories\Form.vue"
        <script>
        export default {
            name: 'CategoriesForm'
        }
        </script>

        <script setup>
        import FormSection from '@/Components/FormSection.vue'
        import InputError from '@/Components/InputError.vue'
        import InputLabel from '@/Components/InputLabel.vue'
        import PrimaryButton from '@/Components/PrimaryButton.vue'
        import TextInput from '@/Components/TextInput.vue'

        defineProps({
            form: {
                type: Object,
                required: true
            },
            updating: {
                type: Boolean,
                required: false,
                default: false
            }
        })

        defineEmits(['submit'])
        </script>

        <template>
            <FormSection @submitted="$emit('submit')">
                <template #title>
                    {{ updating ? 'Update Category' : 'Create Category' }}
                </template>
                <template #description>
                    {{ updating ? 'Update your category' : 'Create a new category' }}
                </template>
                <template #form>
                    <div class="col-span-6 sm:col-span-4">
                        <InputLabel for="name" value="Name" />
                        <TextInput
                            id="name"
                            v-model="form.name"
                            type="text"
                            class="mt-1 block w-full"
                            autocomplete="name"
                            required
                        />
                        <InputError :message="$page.props.errors.name" class="mt-2" />
                    </div>            
                </template>
                <template #actions>
                    <PrimaryButton>
                        {{ updating ? 'Update' : 'Create' }}                
                    </PrimaryButton>
                </template>
            </FormSection>
        </template>
        ```
12. Diseñar las vistas para las lecciones:
    1. Crear vista **Lessons/Index**:
        ```html title="resources\js\Pages\Lessons\Index.vue"
        ```
    2. Crear vista **Lessons/Create**:
        ```html title="resources\js\Pages\Lessons\Create.vue"
        ```
    3. Crear vista **Lessons/Edit**:
        ```html title=""
        ```
    4. Crear componente **Components/Lessons/Form.vue**:
        ```html title="resources\js\Components\Lessons\Form.vue"
        ```
    5. Crear componente **Components/Common/CollectionSelector**:
        ```html title="resources\js\Components\Common\CollectionSelector.vue"
        ```
13. Diseñar las vistas para los roles:
    1. Crear vista **Roles/Index**:
        ```html title=""
        ```
    2. Crear vista **Roles/Create**:
        ```html title=""
        ```
    3. Crear vista **Roles/Edit**:
        ```html title=""
        ```
    4. Crear componente **Components/Roles/Form.vue**:
        ```html title=""
        ```
14. mmmm


