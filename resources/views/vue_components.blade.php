
<style>
    #item-names{
        margin-left: 0px;
        margin-top: 2px;
        margin-bottom: 2px;
    }
</style>

<script>

    /**************************** File Explorer Starts **********************/

    Vue.component('file_explorer',
        {
            template: `
                   <transition name="fade">
                   <div >
                    <div class="row" v-show="shown">
                     <ul style="list-style-type: none;">
                        <button  v-if="currentParent!==0"  class="btn btn-labeled btn-default" style=" cursor: pointer; margin-bottom: 10px" @click="SetParent(previousParent)">
                            <span class="btn-label"><i class="glyphicon glyphicon-chevron-left"></i></span>Go Back
                        </button> <span  if="currentCategory.id!=0" style="font-size: 20px; margin-left: 10px;" >@{{ currentCategory.category_name }}</span><br>
						<div class="itemholder">
                            <li  class="folder" v-for="(aChild, index) in children" v-if="aChild.type=='category' && index>=start_index && index<=end_index" @click="SetParent(aChild.id)">
                                <div class="vertical-align">
                                    <i class="fa fa-folder">
                                    </i> @{{ aChild.category_name }}

                                </div>
                            </li>
                            <li class="product-icon"   v-for="(aChild, index) in children" v-if="aChild.type=='product' && index>=start_index && index<=end_index" @click="ChooseProduct(aChild)"  v-bind:style="{ backgroundImage: 'url(' + aChild.image + ')' }">
                                <div class="vertical-align">
                                    <div v-if="aChild.item_name.length>22">
                                        @{{ aChild.item_name.substr(0,22)}}...
                                    </div>
                                    <div v-else>
                                       @{{ aChild.item_name }}
                                    </div>

                                    <br />
                                    \$@{{ aChild.unit_price }}
                                </div>
                            </li>
                        </div>
                     </ul>
						  <div style="clear: both;"></div>
                                 <ul class="pagination" style="margin-top:0px" v-if="total_page>0" style="margin-left: 40px">
                                     <li class="page-item"><a class="page-link" @click="ShowPageItem(current_page-1)" href="#">Previous</a></li>
                                    <li class="page-item"  v-for="index in total_page" @click="ShowPageItem(index)" v-bind:class="{active:index==current_page}"><a class="page-link" href="#">@{{index}}</a></li>
                                    <li class="page-item"><a @click="ShowPageItem(current_page+1)" class="page-link" href="#">Next</a></li>
                                 </ul>

                            </div>
                </div>
                </div>
                </transition>
                <h1 v-show="!shown" >Loading....</h1>`,
            props: ['shown'],
            data: function(){
                return {
                    /*categoryList: [],
                    productList: [],*/
                    children: [],
                    currentParent: 0,
                    previousParent: 0,
                    current_page: -1,
                    total_page: -1,
                    per_page_item: 20,
                    start_index: 0,
                    end_index: 0,
                    currentCategory: {
                        id: 0,
                        category_name: ''
                    }
                }
            },
            mounted: function () {
                this.SetParent(0);
            },

            methods:
                {
                    ChooseProduct: function(product)
                    {
                        this.$emit('choose-item', product);
                    },
                    FetchProducts: function(category_id){
                        var that = this;
                        this.GetCategoryData(this.currentParent).then((data) => {
                            if(data!==null) {
                                that.currentCategory.id = data.id;
                                that.currentCategory.category_name = data.category_name;
                                console.log(that.currentCategory);
                            }else{
                                that.currentCategory.id = 0;
                                that.currentCategory.category_name = '';
                                console.log(that.currentCategory);
                            }
                        }).then((result) => {

                            axios.get("{{route('products_by_categories')}}"+"?category_id="+category_id,)
                                .then(function (response) {
                                    response.data.data.forEach(function(product) {

                                        let productDetails = {
                                            type: 'product',
                                            item_id : product.item_id,
                                            item_name : product.item_name,
                                            company_name : product.company_name,
                                            item_quantity : product.item_quantity,
                                            unit_price : product.selling_price,
                                            cost_price: product.cost_price,
                                            items_sold : 1,
                                            price_rule_id: product.price_rule_id,
                                            image: product.directory+"/"+product.new_name
                                        };
                                        if(product.discountApplicable)
                                        {
                                            productDetails.discount_applicable = true;
                                            if(this.allDiscountAmountPercentage==0)
                                                productDetails.item_discount_percentage = product.discountPercentage;
                                            else
                                                productDetails.item_discount_percentage = this.allDiscountAmountPercentage;
                                        }
                                        else
                                        {
                                            productDetails.discountApplicable = false;
                                            productDetails.item_discount_percentage = 0;
                                        }
                                        that.children.push(productDetails);
                                    });
                                    that.total_page = Math.ceil(  that.children.length/that.per_page_item );
                                    that.ShowPageItem(1);
                                })
                                .catch(function (error) {
                                    console.log(error);
                                });
                        });
                        //this.children = [];

                    },
                    FetchParent: function(category_id) {
                        return axios.get("{{route('category_parent')}}"+"?category_id="+category_id,)
                            .then(function (response) {

                                return response.data.data;

                            })
                            .catch(function (error) {
                                console.log(error);
                            });
                    },
                    SetParent: function(categoryLevel) {
                        console.log(categoryLevel);
                        var that = this;
                        this.children = [];
                        this.currentParent = categoryLevel;
                        if(categoryLevel!=0) {
                            this.FetchParent(this.currentParent).then((parent) => {
                                that.previousParent = parent;
                            });
                        }
                        this.FetchCategories(categoryLevel);
                        this.FetchProducts(categoryLevel);
                    },
                    FetchCategories: function(categoryLevel){
                        var that = this;
                        axios.get("{{route('categories_by_level')}}"+"?category_level="+categoryLevel,)
                            .then(function (response) {
                                //this.categoryList = response.data.data;
                                response.data.data.forEach(function(category) {
                                    let categoryDetails = {
                                        type: 'category',
                                        category_name: category.category_name,
                                        parent: category.parent,
                                        id: category.id
                                    };
                                    that.children.push(categoryDetails);
                                });
                                that.total_page = Math.ceil(  that.children.length/that.per_page_item );
                                that.ShowPageItem(1);
                            })
                            .catch(function (error) {
                                console.log(error);
                            });
                    },
                    ShowPageItem: function(index)
                    {
                        if(index>this.total_page || index <1 ) return;
                        this.current_page = index;
                        this.start_index = (this.current_page-1) * this.per_page_item;
                        this.end_index = this.current_page * this.per_page_item - 1;
                    },
                    GetCategoryData: function(category_id) {
                        return axios.get("{{route('get_category_data')}}"+"?category_id="+category_id,)
                            .then(function (response) {
                                return response.data.data;

                            })
                            .catch(function (error) {
                                console.log(error);
                            });
                    }
                }
        });


    /*************************** End of file explorer **********************/



    /********autocomplete starts*******/

    Vue.component('auto-complete', {
        template: `<span>
                            <input type="text" ref="inlineTextBox"  class="form-control" id ="item-names" v-model="item_names" @keyup.down="onArrowDown" @keyup.up="onArrowUp" @keyup.enter="onEnter" style="">
                            <ul ref="autoSuggestion" id="autocomplete-results"
                                v-show="isOpen"
                                class="autocomplete-results"
                                @mouseleave="ClearSelection"
                            >
                                  <li
                                          class="loading"
                                          v-if="isLoading"
                                  >
                                    Loading results...
                                  </li>
                                  <li v-else v-for="(item, i) in results" :key="i" @click="setResult(item)" class="autocomplete-result" :class="{ 'is-active': i === arrowCounter }"  @mouseover="SetCounter(i)">
                                    <img height="50px" :src="GetImageUrl(item)" width="50px" style="margin-right:10px" />
                                    <a href="javascript:void(0);">@{{item.item_name}}</a>
                                </li>
                            </ul>
                        </span>`,
        props: ['autoSelect'],
        data: function(){
            return {
                isOpen: false,
                item_names: "",
                results: [],
                arrowCounter: -1,
                isLoading: false,
            }
        },
        methods: {
            SearchProduct() {
                var that = this;
                if(this.item_names=="")
                    return;
                if(this.autoSelect)
                {
                    axios.get("{{route('item_list_autocomplete')}}", {
                        params: { q: this.item_names, autoselect: true }
                    })
                        .then(function (response) {
                            if( response.data.length==1 )
                            {
                                that.setResult(response.data[0])
                            }
                        })
                        .catch(function (error) {
                            console.log(error);
                        });
                }
                else
                {
                    axios.get("{{route('item_list_autocomplete')}}", {
                        params: { q: this.item_names, autoselect: false }
                    })
                        .then(function (response) {
                            that.isOpen = true;
                            that.results = response.data
                        })
                        .catch(function (error) {
                            console.log(error);
                        });
                }
            },
            setResult(product) {
                this.search = product;
                this.$emit('set-autocomplete-result', product);
                this.isOpen = false;
                this.results = [];
                this.arrowCounter = -1;
                this.item_names = "";
                document.getElementById("item-names").focus();
            },
            onArrowDown(evt) {
                if (this.arrowCounter < this.results.length && !this.autoSelect) {
                    this.arrowCounter = this.arrowCounter + 1;
                }
            },
            onArrowUp() {
                if (this.arrowCounter > 0 && !this.autoSelect) {
                    this.arrowCounter = this.arrowCounter -1;
                }
            },
            onEnter() {
                if(this.arrowCounter>=0 && !this.autoSelect)
                    this.setResult(this.results[this.arrowCounter]);
                else if(this.autoSelect && this.item_names.length>0)
                    this.SearchProduct();

            },
            handleClickOutside(evt) {
                if (!this.$el.contains(evt.target)) {
                    this.isOpen = false;
                    this.arrowCounter = -1;
                }
            },
            SetCounter(index)
            {
                this.arrowCounter = index;
            },
            ClearSelection()
            {
                this.arrowCounter = -1;
            },
            GetImageUrl(item)
            {
                var img_src = "default-product.jpg";

                if(item.new_name!=null){
                    img_src = item.new_name;
                }

                var imageUrl = "";

                if(item.product_type==1){
                    imageUrl = '{{asset("img")}}/' + "item-kit.png";
                } else{
                    imageUrl = '{{asset("img")}}/' + img_src;
                }
                return imageUrl;
            }
        },
        created: function () {
            this.debouncedSearch = _.debounce(this.SearchProduct, 1500)
        },
        watch: {
            items: function (val, oldValue) {
                if (val.length !== oldValue.length) {
                    this.results = val;
                    this.isLoading = false;
                }
            },
            item_names: function (newValue, oldValue) {
                if(newValue!=oldValue && newValue!="")
                    this.debouncedSearch()
            }
        },
        mounted() {
            document.addEventListener('click', this.handleClickOutside);
            var inputBoxWidth = this.$refs.inlineTextBox.offsetWidth;
            this.$refs.autoSuggestion.style.width = inputBoxWidth+'px';
        },
        destroyed() {
            document.removeEventListener('click', this.handleClickOutside)
        }
    })
    /********autocomplete ends*******/

    /********** inline edit starts**************/
    Vue.component('inline-edit', {
        template: `<span>
                            <a v-if="!editMode" @click="setEditMode()" href="javascript: void(0);"> <currency-input currency-symbol="$" :value="value"></currency-input></a>
                            <span v-else>

                                <input type="text" v-model="editedValue" class="form-control">
                                <i class="fa fa-check" @click="setValue"></i>
                                <i class="fa fa-times-circle" @click="closeEdit"></i>
                            </span>
                        </span>`,
        props: ['value','ifUserPermitted'],
        data: function()
        {
            return {editMode: false,editedValue:""};
        },
        methods:{
            setEditMode: function(){
                if(!this.ifUserPermitted)
                    return;
                this.editMode = true;
                this.editedValue = this.value;
            },
            setValue()
            {

                this.editMode = false;
                this.$emit('input', this.editedValue)
            },
            closeEdit()
            {
                this.editMode = false;
            }
        }
    });
    /********** inline edit ends**************/

    /*************customer select2************/
    Vue.component('select2', {
        props: ['options', 'value'],
        template: `<select>
        <slot></slot>
    </select>`,
        mounted: function () {

            var vm = this
            $(this.$el)
            // init select2
                .select2({ data: this.options })
                .val(this.value)
                .trigger('change')
                // emit event on change.
                .on('change', function () {
                    vm.$emit('input', this.value)
                });
        },
        watch: {
            value: function (value) {
                // update value
                $(this.$el)
                    .val(value)
                    .trigger('change')
            },
            options: function (options) {
                // update options
                $(this.$el).empty().select2({ data: options })
            }
        },
        destroyed: function () {
            $(this.$el).off().select2('destroy')
        }
    })

    /***********customer select2 ends***********************/

    /********************currency symbol******************/
    Vue.component('currency-input', {
        template: `<span>
                            @{{ localValue>=0 ? currencySymbol : '-' +  currencySymbol }}@{{Math.abs(localValue).toFixed(2)}}
                        </span>`,
        props: ['value','currencySymbol'],
        data: function()
        {

            return {

                localValue: Number(this.value).toFixed(2)
            }
        },
        methods:{
        },
        watch: {
            value:function(value)
            {

                this.localValue = Number(this.value).toFixed(2)
                console.log(this.localValue)
            }
        }
    });
    /*****************************************************/




    /***************************************Counter Change*****************/

    function selectCounter(){

        @if(\Illuminate\Support\Facades\Cookie::get('counter_id')==null)
        $("#choose_counter_modal").modal();
        $.ajax({
            url: "{{route('counter_list_ajax')}}",
            type:"get",
            dataType: "json",
            success: function(response){
                $(".choose-counter-home").html("");
                counters = response.counters;
                counters.forEach(function(counter){
                    var url = '{{ route("counter_set", ":counter_id") }}';
                    url = url.replace(':counter_id', counter.id);
                    $(".choose-counter-home").append('<li><a class="set_employee_current_counter_after_login" href="'+url+'">'+counter.name+'</a></li>');
                });
            },
            error: function () {

            }
        })
        @endif
    }

    function changeCounter(){

        $.ajax({
            url: "{{route('counter_list_ajax')}}",
            type:"get",
            dataType: "json",
            success: function(response){
                $("#choose_counter_modal").modal();
                $(".choose-counter-home").html("");
                counters = response.counters;
                counters.forEach(function(counter){
                    var oneCounterHtml = '<li><a class="set_employee_current_counter_after_login" href="javascript:void(0)" data-counter-id="' + counter.id + '">'+counter.name+'</a></li>';
                    var counterJqueryElement = $(oneCounterHtml);

                    counterJqueryElement.find('.set_employee_current_counter_after_login').click(function(){
                        var counterId = $(this).attr("data-counter-id");
                        var url = '{{ route("counter_set_ajax", ":counter_id") }}';
                        url = url.replace(':counter_id', counterId);

                        $.ajax({
                            url: url,
                            type:"get",
                            dataType: "json",
                            success: function(response){
                                $("#choose_counter_modal").modal('hide');
                                $(".counter-name").html('<b>'+response.name+'</b>');
                            }
                        });
                    });
                    $(".choose-counter-home").append(counterJqueryElement);
                });
            },
            error: function () {

            }
        })
    }



    /******************************Counter Change ****************************/
</script>