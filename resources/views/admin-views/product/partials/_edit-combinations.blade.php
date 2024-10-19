@if(count($combinations) > 0)
    <table class="table table-bordered">
        <thead>
        <tr>
            <td class="text-center">
                <label for="" class="control-label">Variant</label>
            </td>
            <td class="text-center">
                <label for="" class="control-label">Variant Price</label>
            </td>
            <td class="text-center">
                <label for="" class="control-label">Variant Image</label>
            </td>
            <td class="text-center">
                <label for="" class="control-label">Upload Variant Image</label>
            </td>
        </tr>
        </thead>
        <tbody>

        @foreach ($combinations as $key => $combination)
            <tr>
                <td>
                    <label for="" class="control-label">{{ $combination['type'] }}</label>
                </td>
                <td>
                    <input type="number" name="price_{{ $combination['type'] }}"
                           value="{{$combination['price']}}" min="0"
                           step="0.01"
                           class="form-control" required>
                </td>
                
                <td>
                    @if(isset($combination['image']))
                    @if($combination['image'] != null) 
                        <img style="height: 100px;border: 1px solid; border-radius: 10px;" src="{{asset('storage/app/public/product')}}/{{$combination['image']}}" alt="Variant Image"/>
                    @endif
                    @endif
                </td>
                <td>
                    <input type="file" name="image_{{ $combination['type'] }}" class="form-control" accept="image/png, image/jpg, image/jpeg">
                    @if(isset($combination['image']))
                    @if($combination['image'] != null) 
                        <input type="hidden" value="{{$combination['image']}}" name="old_image_{{ $combination['type'] }}">
                    @else
                        <input type="hidden" value="" name="old_image_{{ $combination['type'] }}">
                    @endif
                    @endif
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
@endif
