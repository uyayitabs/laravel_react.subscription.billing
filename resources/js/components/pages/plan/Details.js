import React from "react";

import { GetPlan, UpdatePlan, GetDependenciesPlans } from '../../controllers/plans';
import PlanLines from "./plan_lines/List";

import DetailsPage from "../../layouts/DetailsPage";
import Details from "../../components/Details";
import Loader from "../../components/Loader";

class PlanDetail extends React.Component {
  constructor(props) {
    super(props);

    this.state = {
      plan: {},
      plan_lines: [],
      loading: true
    }
  }

  componentDidMount() {
    (async () => {
      await GetPlan(null, this.props.match.params.id)
        .then(res => {
          const plan = res.data.data;

          this.setState({
            plan,
            plan_lines: plan.plan_lines
          })

          GetDependenciesPlans()
            .then(res => {
              const data = res.data

              let areaCodeOpts = [],
                planOpts = []

              data.area_codes.data.forEach((area_code) => {
                areaCodeOpts.push({
                  label: area_code.id,
                  value: area_code.id
                })
              })

              data.plans.data.forEach((plan) => {
                let isCurrent = this.state.plan && this.state.plan.id == plan.id

                if (!isCurrent) {
                  planOpts.push({
                    label: plan.description,
                    value: plan.id
                  })
                }
              })

              this.setState({
                areaCodeOpts,
                planOpts,
                details: [{
                  label: 'Area Code',
                  data: 'area_code_id',
                  type: 'select',
                  opts: areaCodeOpts
                }, {
                  label: 'Parent plan',
                  data: 'parent_plan',
                  type: 'select',
                  opts: planOpts
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
                }]            
              })

              this.setState({
                tabs: [{
                  title: 'Details',
                  component: <Details
                    getApi={GetPlan}
                    updateApi={UpdatePlan}
                    id={this.props.match.params.id}
                    details={this.state.details}
                    quill="description_long" />
                }, {
                  title: 'Plan Lines',
                  component: <PlanLines id={this.props.match.params.id} />
                }],
                loading: false
              })
            })
            .catch(err => {
              console.log(err)
            })
        })
        .catch(err => {
          console.log(err)
        })
    })()
      .catch(err => {
        console.log(err)
      })
  }

  render() {
    const { loading, areaCodeOpts, planOpts, tabs } = this.state

    return (
      <React.Fragment>
        {!loading && areaCodeOpts && planOpts && tabs ?
          <DetailsPage
            name="Plan"
            tabs={tabs}
          /> : <Loader />
        }
      </React.Fragment>
    )
  }
}

export default PlanDetail;
