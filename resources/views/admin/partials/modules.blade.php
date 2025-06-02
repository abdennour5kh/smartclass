
<style>
    .colorPickerCircle {
      width: 20px;
      height: 20px;
      border-radius: 50%;
      border: 1px solid #aaa;
      padding: 0;
      appearance: none;
      -webkit-appearance: none;
      cursor: pointer;
      background-color: transparent;
      transition: box-shadow 0.5s ease;
      margin-left: 10px;
    }

    .colorPickerCircle:hover {
      box-shadow: 0 0 0 2px #ccc;
    }

    /* Clean up WebKit style */
    .colorPickerCircle::-webkit-color-swatch-wrapper {
      padding: 0;
    }

    .colorPickerCircle::-webkit-color-swatch {
      border: none;
      border-radius: 50%;
    }
  </style>
<!-- Form to add a new semester -->
<form action="{{ route('admin_store_module') }}" method="POST" class="mb-3" enctype="multipart/form-data">
     @csrf 
    <div class="row mb-3">
        <div class="col-md-4">
            <div class="input-group">
                <input type="text" name="name" class="form-control" placeholder="Add new section (e.g., System d'exploitation)" required>
            </div>
        </div>
        <div class="col">
            <div class="input-group">
                <select name="promotion_id" id="promotion" class="form-control promotion-select" required>
                <option value="">Select Promotion</option> 
                    @foreach($promotions as $promotion) 
                        <option value="{{ $promotion->id }}">{{ $promotion->name }}</option> 
                    @endforeach
                </select>
            </div>
        </div>
        <div class="col">
        <div class="input-group">
                               
                                <select name="semester_id" id="semester" class="form-control semester-select" required>
                                    <option value="">Select Semester</option>
                                </select>
                            </div>
        </div>
        <div class="col">
        <div class="input-group">
                                            
                                            <input class="form-control" type="file" id="image" name="image" accept="image/*">
                                            </div>
        </div>
  </div>
  <div class="row">
    <div class="col-md-3">
        <div class="input-group align-items-center">
        <input type="text" class="form-control customColorInput" name="color" placeholder="Pick a color, eg: red or #ff0000">
        <input type="color" class="colorPickerCircle" value="#813d9c" title="Pick a color">

        </div>
    </div>
  </div>
  <button type="submit" class="btn btn-primary mt-3">Add</button>
</form>

@if (!empty($promotions))
    <div class="">
        <div class="list-group">

            @foreach ($promotions as $promo)
                @foreach ($promo->semesters as $semester)
                    @foreach ($semester->modules as $module)
                        <!-- Item -->
                    <div class="list-group-item structureListGroupItem moduleListGroup"
                        data-promotion-id="{{ $promo->id }}"
                        data-semester-id="{{ $semester->id }}"
                        data-section="{{ $module->id }}">
                        <div class="row align-items-center">
                            <div class="col-md-6">
                                <h6 class="text-muted m-0">{{ $module->name }} - {{ $semester->name }} - {{ $promo->name }}</h6>
                            </div>
                            <div class="col-md-6 text-md-end">
                                <div class="d-flex flex-wrap justify-content-md-end">
                                    <button class="btn btn-sm btn-inverse-secondary"
                                        data-bs-toggle="collapse"
                                        data-bs-target="#semesterDetails{{ $module->id }}"
                                        aria-expanded="false">
                                        details
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="collapse mt-2" id="semesterDetails{{ $module->id }}">
                            <div class="d-flex flex-wrap justify-content-between mt-3 mb-2">
                                <p class="mb-1">🗓 Creation Date: {{ $module->created_at }}</p>
                                <p class="mb-1">🗓 Last Edited: {{ $module->updated_at }}</p>
                            </div>
                            
                            <p class="text-muted">Edit Module Name and Logo:</p>
                            <div class="row">
                                <div class="col-md-12">
                                    <form action="{{ route('admin_update_module', $module->id) }}" method="POST" class="mb-3" enctype="multipart/form-data">
                                        @csrf
                                        <div class="row">
                                            <div class="col-md-8">
                                                <div class="input-group">
                                                    <input type="text" name="name" class="form-control" value="{{ old('name', $module->name ?? '') }}">
                                                    <input type="hidden" name="semester_id" value="{{ $semester->id }}">
                                                
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="input-group align-items-center">
                                                <input type="text" class="form-control customColorInput" name="color" value="{{ $module->color }}" placeholder="{{ $module->color }}">
                                                <input type="color" class="colorPickerCircle" value="{{ $module->color }}" title="Pick a color">

                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="d-flex align-items-center gap-3 mt-3">
                                            @if ($module->img_url)
                                            <img src="{{ asset('storage/'.$module->img_url) }}" 
                                                alt="Module Image" 
                                                class="rounded shadow-sm" 
                                                width="100" 
                                                height="100">
                                            @else
                                            <img src="{{ asset('images/default-image.jpg') }}" 
                                                alt="Module Image" 
                                                class="rounded shadow-sm" 
                                                width="100" 
                                                height="100">
                                            @endif
                                            <input class="form-control" type="file" id="image" name="image" accept="image/*">
                                        </div>
                                        <button type="submit" class="btn btn-primary rounded mt-4">Update</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                @endforeach
            @endforeach

        </div>
    </div>
@else
    <!-- leave empty -->
@endif

<script>
  document.querySelectorAll('.colorPickerCircle').forEach((picker, index) => {
    const input = picker.parentElement.querySelector('.customColorInput');

    // Color picker → Text input
    picker.addEventListener('input', () => {
      input.value = picker.value;
    });

    // Text input → Color picker (if valid)
    input.addEventListener('input', () => {
      const value = input.value.trim();
      const temp = document.createElement('div');
      temp.style.color = value;
      if (temp.style.color !== '') {
        const ctx = document.createElement('canvas').getContext('2d');
        ctx.fillStyle = value;
        picker.value = rgbToHex(ctx.fillStyle);
      }
    });
  });

  function rgbToHex(rgb) {
    const ctx = document.createElement('canvas').getContext('2d');
    ctx.fillStyle = rgb;
    return ctx.fillStyle;
  }
</script>
