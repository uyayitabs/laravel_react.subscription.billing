import React from "react";

import { GetPlanLine, UpdatePlanLine, GetDependenciesPlanLines } from '../../../controllers/plans';
import PlanLinePrices from "./plan_line_prices/List";

import DetailsPage from "../../../layouts/DetailsPage";
import Details from "../../../components/Details";
import Loader from "../../../components/Loader";

class PlanLineDetail extends React.Component {
  constructor(props) {
    super(props)

    this.state = {
      plan_line: {},
      plan_line_prices: null,
      loading: true
    }
  }

  componentDidMount() {
    (async () => {
      await GetDependenciesPlanLines()
        .then(res => {
          const datas = res.data,
            products = datas.products.data,
            plan_line_types = datas.plan_line_types.data,
            plan_lines = datas.plan_lines.data

          let productOpts = [],
              planLineTypesOpts = [],
              planLineOpts = []

          products.forEach((product) => {
            productOpts.push({
              label: product.description,
              value: product.id
            });
          })

          plan_line_types.forEach((plan_line_type, ) => {
            planLineTypesOpts.push({
              label: plan_line_type.line_type,
              value: plan_line_type.id
            })
          })

          plan_lines.forEach((plan_line) => {
            if (parseInt(plan_line.id) !== parseInt(this.props.match.params.plid)) {
              planLineOpts.push({
                label: plan_line.description,
                value: plan_line.id
              });
            }
          })

          this.setState({
            productOpts,
            planLineOpts,
            planLineTypesOpts,
            details: [{
              label: 'Product',
              data: 'product_id',
              type: 'select',
              opts: productOpts
            }, {
              label: 'Plan Line Type',
              data: 'plan_line_type.id',
              type: 'select',
              opts: planLineTypesOpts
            }, {
              label: 'Parent Plan Line',
              data: 'parent_plan_line_id',
              type: 'select',
              opts: planLineOpts
            }, {
              label: 'Mandatory Line',
              data: 'mandatory_line',
              type: 'checkbox'
            }, {
              label: 'Plan Start',
              data: 'plan_start',
              type: 'datepicker'
            }, {
              label: 'Plan Stop',
              data: 'plan_stop',
              type: 'datepicker'
            }, {
              label: 'Description',
              data: 'description',
              type: 'text'
            }],
          })

          this.setState({
            tabs: [{
              title: 'Details',
              component: <Details
                id={ this.props.match.params.plid }
                getApi={ GetPlanLine }
                updateApi={ UpdatePlanLine }
                id={ this.props.match.params.plid }
                details={ this.state.details } 
                api={ UpdatePlanLine } 
                quill="description_long" />
            },
              {
                title: 'Plan Line Prices',
                component: <PlanLinePrices id={ this.props.match.params.id } id2={ this.props.match.params.plid } />
              }
            ],
            loading: false
          })
        })
        .catch(err => {
          console.log(err)
        });
    })()
      .catch(err => {
        console.log(err)
      })
  }

  render() {
    const { loading, tabs } = this.state

    return (
      <React.Fragment>
        { !loading && tabs ?
          <DetailsPage
            name="Plan Line"
            tabs={ tabs }
          /> : <Loader />
        }
      </React.Fragment>
    )
  }
}

export default PlanLineDetail;
