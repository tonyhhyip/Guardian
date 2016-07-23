<?hh

if (!function_exists('preference')) {
    use App\Models\Config;
    use Illuminate\Support\Facades\Cache;
    use Illuminate\Database\Eloquent\ModelNotFoundException;

    /**
     * @param string $name
     * @param mixed $fallback
     *
     * @return mixed|null
     */
    function preference(string $name, mixed $fallback = null): mixed
    {
        $value = env($name);
        if ($value !== null) {
            return $value;
        }

        $value = config($name);
        if ($value !== null) {
            return $value;
        }

        // Try to load from cache
        $value = Cache::get("config:{$name}");
        if ($value !== null) {
            return $value;
        }

        try {
            $modal = Config::findOrFail($name);
            $value = $modal->value;
            Cache::set("config:{$name}", $value);
        } catch (ModelNotFoundException $e) {
            Cache::set("config:{$name}", $fallback);
            return $fallback;
        }

        return $value;
    }
}