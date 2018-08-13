
<template>
    <transition name="fade">
        <div class="row" style="margin-left: -25px">
            <div v-show="shown">
                <ul style="list-style-type: none;">
                    <button  v-if="currentParent!==0"  class="btn btn-labeled btn-default" style=" cursor: pointer; margin-bottom: 10px" @click="SetParent(previousParent)">
                    <span class="btn-label"><i class="glyphicon glyphicon-chevron-left"></i></span>Go Back
                    </button> <span  if="currentCategory.id!=0" style="font-size: 20px; margin-left: 10px;" >@{{ currentCategory.category_name }}</span><br>
                    <li  class="folder" v-for="(aChild, index) in children" v-if="aChild.type=='category' && index>=start_index && index<=end_index" @click="SetParent(aChild.id)">
                        <div class="vertical-align">
                            <i class="fa fa-folder">
                            </i> @{{ aChild.category_name }}
                        </div>
                    </li>
                    <li class="product-icon " v-for="(aChild, index) in children" v-if="aChild.type=='product' && index>=start_index && index<=end_index" @click="ChooseProduct(aChild)">
                        <div class="vertical-align">
                            <div v-if="aChild.item_name.length>22">
                                @{{ aChild.item_name.substr(0,22)}}...
                            </div>
                            <div v-else>
                                @{{ aChild.item_name }}
                            </div>

                            <br />
                            $@{{ aChild.unit_price }}
                        </div>
                    </li>
                </ul>
            <div style="clear: both;"></div>
                <br>
                <ul>
                    <button class="btn btn-labeled btn-default"  @click="ShowPageItem(current_page-1)" style=" cursor: pointer; margin-bottom: 10px" >
                    <span class="btn-label"><i class="glyphicon glyphicon-chevron-left"></i></span>Previous
                    </button>
                    <span v-if="total_page>0" style="margin-left: 420px">Showing page @{{ current_page }} of @{{ total_page }} page(s)</span>
                    <button @click="ShowPageItem(current_page+1)" class="btn btn-labeled btn-default" style=" cursor: pointer; margin-bottom: 10px; float:right; margin-right: 80px;" >
                    <span class="btn-label"><i class="glyphicon glyphicon-chevron-right"></i></span>Next&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                          </button>
                </ul>
            </div>
            </div>
            </transition>
        <h1 v-show="!shown" >Loading....</h1>
</template>


<script>
    export  default{
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
                per_page_item: 26,
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
                                        price_rule_id: product.price_rule_id
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
    }
</script>