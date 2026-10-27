<select id="{{ $id }}" class="form-select product_category_select" name="Productcategory" required>
    <option value="">-- Choose Category --</option>
    @foreach ($categories as $data)
        <option value="{{ $data->id }}" {{ $selected == $data->id ? 'selected' : '' }}>
            {{ $data->category_name }}
        </option>
    @endforeach
</select>