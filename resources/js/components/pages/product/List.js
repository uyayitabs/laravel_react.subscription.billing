import React from "react";

import { GetProducts } from '../../controllers/products';
import ProductForm from "./Form";
import ParentTable from '../../components/ParentTable';

const Products = () => {
  return(
    <ParentTable
        table="Products"
        data={ GetProducts }
        include="product-type"
        columns={[
          {
            dataField: "description",
            text: "Description",
            sort: true
          }, {
            dataField: "price",
            text: "Price",
            sort: true
          }, {
            dataField: "product_type.type",
            text: "Product Type",
            sort: true
          }, {
            dataField: "status",
            text: "Status",
            sort: true            
          }
        ]}
        action="edit"
        form={ ProductForm }
      />
  )
}

export default Products;
