import React from "react";
import moment from 'moment';

import { Card, CardBody, CardHeader, Container, Row, Col, CardTitle, Table, Button } from "reactstrap";

import { GetSubscriptionLine, GetSubscriptionLines, UpdateSubscriptionLine, GetPlanSubscriptionLineTypes } from '../../../controllers/subscriptions';
import SubscriptionLinePrices from "./subscription_line_prices/List";
import DetailsPage from "../../../layouts/DetailsPage";
import Details from "../../../components/Details";
import Loader from "../../../components/Loader";

class SubscriptionLineDetail extends React.Component {

    constructor(props) {
        super(props);

        this.state = {
            loading: true
        }
    }

    componentDidMount() {
        (async () => {
            await GetPlanSubscriptionLineTypes()
                .then(res => {
                    const subscription_line_type = res.data.data;

                    let subscriptionLineTypesOpts = [];
                    subscription_line_type.forEach((subscription_line_type, ) => {
                        subscriptionLineTypesOpts.push({
                            label: subscription_line_type.line_type,
                            value: subscription_line_type.id
                        })
                    });

                   this.setState({
                    details: [{
                        label: 'Product',
                        data: 'product.description',
                        type: 'text',
                        disabled: true
                    }, {
                        label: 'Subscription Line Type',
                        data: 'subscription_line_type',
                        type: 'select',
                        opts: subscriptionLineTypesOpts
                    }, {
                        label: 'Mandatory Line',
                        data: 'mandatory_line',
                        type: 'checkbox'
                    }, {
                        label: 'Serial',
                        data: 'serial',
                        type: 'text',
                        disabled: true
                    }, {
                        label: 'Subscription Start',
                        data: 'subscription_start',
                        type: 'datepicker'
                    }, {
                        label: 'Subscription Stop',
                        data: 'subscription_stop',
                        type: 'datepicker'
                    }, {
                        label: 'Description',
                        data: 'description',
                        type: 'text',
                        disabled: true
                    }]
                })

                this.setState({
                    subscriptionLine: {},
                    subscriptionLinePrices: null,
                    tabs: [{
                        title: 'Details',
                        component: <Details 
                            getApi={ GetSubscriptionLine }  
                            updateApi={ UpdateSubscriptionLine }
                            id={ this.props.match.params.slid } 
                            quill="description_long"
                            details={ this.state.details } 
                            api={ UpdateSubscriptionLine } />
                    },   {
                        title: 'Subscription Line Prices',
                        component: <SubscriptionLinePrices id={ this.props.match.params.id } id2={ this.props.match.params.slid } />
                    }],
                    loading: false,

                    subscriptionLineTypesOpts
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
                        name="Subscription Line"
                        tabs={ tabs }
                    /> : <Loader />
                }
            </React.Fragment>
        );
    }

    // render() {
    //     const SubscriptionLineDetails = () => (
    //         <Card>
    //             <CardHeader>
    //                 <CardTitle tag="h5" className="mb-0 float-left">
    //                     Subscription Line Details
    //                 </CardTitle>
    //                 <Button className="float-right" color="primary" size="md" onClick={this.toggleModal}>Edit</Button>
    //             </CardHeader>
    //             <CardBody>
    //                 <Table className="mb-0">
    //                     <tbody>
    //                         <tr>
    //                             <td>Description: </td>
    //                             <td>{this.state.subscriptionLine.description != undefined ? this.state.subscriptionLine.description : null}</td>
    //                         </tr>
    //                         <tr>
    //                             <td>Line Type:</td>
    //                             <td>{this.state.subscriptionLine.line_type ? this.state.subscriptionLine.line_type.line_type : null}</td>
    //                         </tr>
    //                         <tr>
    //                             <td>Mandatory:</td>
    //                             <td>{this.state.subscriptionLine.mandatory_line ? 'Yes' : 'No'}</td>
    //                         </tr>
    //                         <tr>
    //                             <td>Serial:</td>
    //                             <td>{this.state.subscriptionLine.serial}</td>
    //                         </tr>
    //                         <tr>
    //                             <td>Subscription Start:</td>
    //                             <td>{this.formatDate(this.state.subscriptionLine.subscription_start)}</td>
    //                         </tr>
    //                         <tr>
    //                             <td>Subscription Stop:</td>
    //                             <td>{this.state.subscriptionLine.subscription_stop ? this.formatDate(this.state.subscriptionLine.subscription_stop) : ''}</td>
    //                         </tr>
    //                     </tbody>
    //                 </Table>
    //             </CardBody>
    //         </Card>
    //     )

    //     return (
    //         <Container fluid className="p-0">
    //             <Row>
    //                 <Col lg="4">
    //                     {this.state.subscriptionLine ? <SubscriptionLineDetails /> : null}
    //                 </Col>
    //                 <Col lg="8">
    //                     {this.state.subscriptionLinePrices ? <SubscriptionLinePrices data={this.state.subscriptionLinePrices} /> : null}
    //                 </Col>
    //             </Row>

    //             {this.state.isOpen ?
    //                 <SubscriptionLineForm
    //                     show={this.state.isOpen}
    //                     hide={this.toggleModal}
    //                     id={this.state.subscriptionLine.id}
    //                     action={this.updateComponent}
    //                     data={this.state.subscriptionLine}
    //                 /> : null
    //             }
    //         </Container>
    //     )
    // }
}

export default SubscriptionLineDetail;
