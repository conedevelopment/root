<div class="form-group--row">
    <label class="form-label" for="{{ $attrs->get('id') }}">
        {{ $label }}
        @if ($attrs->get('required'))
            <span style="color: var(--spruce-alert-color-danger);">*</span>
        @endif
    </label>
    <select {{ $attrs->class(['form-control', 'form-control--invalid' => $invalid]) }}>
        <option value="shoes">Shoes</option>
        <option value="caps">Caps</option>
        <option value="jacket">Jacket</option>
    </select>
</div>
