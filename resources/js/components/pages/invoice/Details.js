import React from "react";
import { ExternalLink } from "react-feather";
import ToolkitProvider, { Search } from 'react-bootstrap-table2-toolkit';
import BootstrapTable from "react-bootstrap-table-next";
import paginationFactory from "react-bootstrap-table2-paginator";
import { Link } from "react-router-dom";
import { toastr } from "react-redux-toastr";
import Loader from '../../components/Loader';

import { Card, CardBody, CardHeader, Container, Row, Col, CardTitle, Badge, Table, Button, ListGroup, ListGroupItem, UncontrolledDropdown, DropdownMenu, DropdownItem, DropdownToggle  } from "reactstrap";
import { MoreHorizontal } from "react-feather";
import { GetInvoice, GenerateInvoice, SendInvoicEmail } from '../../controllers/invoices';
// import SalesInvoiceForm from "./Form";
import AuthService from '../../services/authService';

const Auth = new AuthService();

const { SearchBar } = Search;

const ListGroupStyle = {
    borderTop: '1px solid #dee2e6',
    marginBottom: '10px'
}

const ListGroupItemStyle = {
    border: 0
}

class InvoiceDetails extends React.Component {
    constructor(props) {
        super(props);

        this.state = {
            sales_invoice: null,
            isOpen: false,
            loading: true,
        }

        this.actionsFormatter = (cell, row, rowIndex, formatExtraData) => {
            return (
                <Link to={"/invoices_line/" + row.id + "/detail"}><ExternalLink /></Link>
            );
        }

        this.toggleModal = this.toggleModal.bind(this);
        // this.updateComponent = this.updateComponent.bind(this);
        this.generateInvoice = this.generateInvoice.bind(this);
        this.sendInvoiceEmail = this.sendInvoiceEmail.bind(this);
        console.log(props.sales_invoice)
    }

    showToastr(type, title, msg) {
        const opts = {
            timeOut: 5000,
            showCloseButton: false,
            progressBar: false
        };

        const toastrInstance =
            type === 201 ? toastr.success
                : toastr.error;

        toastrInstance(title, msg, opts);
    }

    toggleModal() {
        this.setState({ isOpen: !this.state.isOpen });
    }

    generateInvoice(e) {
        (async () => {
            await GenerateInvoice(this.state.sales_invoice.id)
                .then(res => {
                    const url = window.URL.createObjectURL(new Blob([res.data]));
                    const link = document.createElement('a');
                    link.href = url;
                    link.setAttribute('download', this.state.sales_invoice.invoice_filename);
                    document.body.appendChild(link);
                    link.click();
                })
                .catch(err => {
                    console.log(err)
                });
        })()
            .catch(err => {
                console.log(err)
            })
        e.preventDefault();
    }

    sendInvoiceEmail(e) {
        (async () => {
            await SendInvoicEmail(this.state.sales_invoice.id)
                .then(res => {
                    if (res.status == 200) {
                        this.showToastr(201, '', 'Email with invoice PDF sent!');
                    } else {
                        this.showToastr(404, '', 'Something went wrong in sending the email!');
                    }
                })
                .catch(err => {
                    console.log(err)
                });
        })()
            .catch(err => {
                console.log(err)
            })
        e.preventDefault();
    }

    // updateComponent(val) {
    //     const data = val[0];
    //     console.log(data);
    //     axios.get(`/api/sales_invoices/${data.id}`)
    //         .then(res => {
    //             console.error
    //             let sales_invoice = Object.assign({}, res.data.data);
    //             this.setState({ sales_invoice: sales_invoice })
    //         })
    //         .catch(err => {
    //             console.log(err.response.data)
    //         })
    // }

    componentDidMount() {
        (async () => {
            await GetInvoice(this.props.match.params.id)
                .then(res => {
                    console.log(res)
                    this.setState({
                        sales_invoice: res.data.data,
                        loading: false
                    });
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
        const InvoiceCardHeader = () => {
            return (
                <CardHeader className="d-flex align-items-center">
                    {this.state.sales_invoice.tenant_name} invoices {this.state.sales_invoice.invoice_no}
                    <UncontrolledDropdown className="ml-auto">
                        <DropdownToggle nav className="px-3 py-2">
                            <MoreHorizontal size={ 18 } />
                        </DropdownToggle>

                        <DropdownMenu right={ true }>
                            <DropdownItem className="py-2" onClick={this.generateInvoice}>View PDF Invoice</DropdownItem>
                            <DropdownItem className="py-2" onClick={this.sendInvoiceEmail}>Email PDF Invoice</DropdownItem>
                            <DropdownItem className="py-2">Create Credit</DropdownItem>
                            <DropdownItem className="py-2">View Journals</DropdownItem>
                            {/* <DropdownItem className="py-2" onClick={this.toggleModal}>Edit</DropdownItem> */}
                        </DropdownMenu>
                    </UncontrolledDropdown>
                </CardHeader>
            );
            
        }
        const BasicDetails = (props) => {
            return (
                <CardBody>
                    <Row className="mt-3">
                        <Col xs="12" md="6">
                            <h5>Invoice Details</h5>
                            <ListGroup style={ ListGroupStyle }>
                                <ListGroupItem className="d-flex align-items-center px-0" style={ ListGroupItemStyle }>
                                    <Col xs="6" md="5" lg="4">Invoice Number:</Col>
                                    <Col xs="6" md="7" lg="8">{props.sales_invoice.invoice_no}</Col>
                                </ListGroupItem>
                                <ListGroupItem className="d-flex align-items-center px-0" style={ ListGroupItemStyle }>
                                    <Col xs="6" md="5" lg="4">Invoice Date:</Col>
                                    <Col xs="6" md="7" lg="8">{props.sales_invoice.date}</Col>
                                </ListGroupItem>
                                <ListGroupItem className="d-flex align-items-center px-0" style={ ListGroupItemStyle }>
                                    <Col xs="6" md="5" lg="4">Due Date:</Col>
                                    <Col xs="6" md="7" lg="8">{props.sales_invoice.due_date}</Col>
                                </ListGroupItem>
                                <ListGroupItem className="d-flex align-items-center px-0" style={ ListGroupItemStyle }>
                                    <Col xs="6" md="5" lg="4">Customer:</Col>
                                    <Col xs="6" md="7" lg="8">{ props.sales_invoice && props.sales_invoice.relation_customer_number ? props.sales_invoice.relation_customer_number : '' }</Col>
                                </ListGroupItem>
                                {/* <ListGroupItem className="d-flex align-items-center px-0">
                                    <Col xs="6" md="5" lg="4">Description:</Col>
                                    <Col xs="6" md="7" lg="8">{props.sales_invoice.description}</Col>
                                </ListGroupItem> */}
                                {/* <ListGroupItem className="d-flex align-items-center px-0">
                                    <Col xs="6" md="5" lg="4">Email:</Col>
                                    <Col xs="6" md="7" lg="8">{props.sales_invoice.relation_email}</Col>
                                </ListGroupItem> */}
                                <ListGroupItem className="d-flex align-items-center px-0" style={ ListGroupItemStyle }>
                                    <Col xs="6" md="5" lg="4">Company Name:</Col>
                                    <Col xs="6" md="7" lg="8">{props.sales_invoice.relation_company_name}</Col>
                                </ListGroupItem>
                                <ListGroupItem className="d-flex align-items-center px-0" style={ ListGroupItemStyle }>
                                    <Col xs="6" md="5" lg="4">VAT number:</Col>
                                    <Col xs="6" md="7" lg="8"></Col>
                                </ListGroupItem>
                            </ListGroup>
                        </Col>
                        <Col xs="12" md="6">
                            <h5>Finance</h5>
                            <ListGroup style={ ListGroupStyle }>
                                <ListGroupItem className="d-flex align-items-center px-0" style={ ListGroupItemStyle }>
                                    <Col xs="6" md="5" lg="4">Total excl. {(props.sales_invoice.vat_percentage > 0 ? props.sales_invoice.vat_percentage : 0.21) * 100}% VAT:</Col>
                                    <Col xs="6" md="7" lg="8">{props.sales_invoice.rounded_price}</Col>
                                </ListGroupItem>
                                <ListGroupItem className="d-flex align-items-center px-0" style={ ListGroupItemStyle }>
                                    <Col xs="6" md="5" lg="4">{(props.sales_invoice.vat_percentage > 0 ? props.sales_invoice.vat_percentage : 0.21) * 100}% VAT:</Col>
                                    <Col xs="6" md="7" lg="8">{props.sales_invoice.rounded_price_vat}</Col>
                                </ListGroupItem>
                                <ListGroupItem className="d-flex align-items-center px-0" style={ ListGroupItemStyle }>
                                    <Col xs="6" md="5" lg="4">Total incl. VAT:</Col>
                                    <Col xs="6" md="7" lg="8">{props.sales_invoice.rounded_price_total}</Col>
                                </ListGroupItem>
                                <ListGroupItem className="d-flex align-items-center px-0" style={ ListGroupItemStyle }>
                                    <Col xs="6" md="5" lg="4">Open amount to pay:</Col>
                                    <Col xs="6" md="7" lg="8"></Col>
                                </ListGroupItem>
                                <ListGroupItem className="d-flex align-items-center px-0" style={ ListGroupItemStyle }>
                                    <Col xs="6" md="5" lg="4">Payment method:</Col>
                                    <Col xs="6" md="7" lg="8"></Col>
                                </ListGroupItem>
                                <ListGroupItem className="d-flex align-items-center px-0" style={ ListGroupItemStyle }>
                                    <Col xs="6" md="5" lg="4">Status:</Col>
                                    <Col xs="6" md="7" lg="8">
                                        <Badge color="primary" className="badge-pill mr-1 mb-1">Open not overdue</Badge>
                                        <Badge color="danger" className="badge-pill mr-1 mb-1">Open and overdue</Badge>
                                        <Badge color="success" className="badge-pill mr-1 mb-1">Paid</Badge>
                                    </Col>
                                </ListGroupItem>
                            </ListGroup>
                        </Col>
                    </Row>
                    <Row className="mt-3">
                        <Col xs="12" md="6">
                            <h5>Billing</h5>
                            <ListGroup style={{ borderTop: '1px solid #dee2e6 '}}>
                                <ListGroupItem className="d-flex align-items-center px-0" style={ ListGroupItemStyle }>
                                    <Col xs="6" md="5" lg="4">Address:</Col>
                                    <Col xs="6" md="7" lg="8">{props.sales_invoice.invoice_address ? props.sales_invoice.invoice_address.full_address : null}</Col>
                                </ListGroupItem>
                                <ListGroupItem className="d-flex align-items-center px-0" style={ ListGroupItemStyle }>
                                    <Col xs="6" md="5" lg="4">Person:</Col>
                                    <Col xs="6" md="7" lg="8">{ props.sales_invoice && props.sales_invoice.relation && props.sales_invoice.relation.persons ? props.sales_invoice.relation.persons[0].full_name : '' }</Col>
                                </ListGroupItem>
                            </ListGroup>
                        </Col>
                        <Col xs="12" md="6">
                            <h5>Shipping</h5>
                            <ListGroup style={{ borderTop: '1px solid #dee2e6 '}}>
                                <ListGroupItem className="d-flex align-items-center px-0" style={ ListGroupItemStyle }>
                                    <Col xs="6" md="5" lg="4">Address:</Col>
                                    <Col xs="6" md="7" lg="8">{props.sales_invoice.shipping_address ? props.sales_invoice.shipping_address.full_address : null}</Col>
                                </ListGroupItem>
                                <ListGroupItem className="d-flex align-items-center px-0" style={ ListGroupItemStyle }>
                                    <Col xs="6" md="5" lg="4">Person:</Col>
                                    <Col xs="6" md="7" lg="8">{ props.sales_invoice && props.sales_invoice.relation && props.sales_invoice.relation.persons ? props.sales_invoice.relation.persons[0].full_name : '' }</Col>
                                </ListGroupItem>
                            </ListGroup>
                        </Col>
                    </Row>
                </CardBody>
            );
        }

        const SalesInvoiceLines = () => {
            return (
                <ToolkitProvider
                    keyField="id"
                    data={this.state.sales_invoice != null && this.state.sales_invoice.sales_invoice_lines != null ? this.state.sales_invoice.sales_invoice_lines : []}
                    columns={[{
                        // dataField: "subscription_line_id",
                        // text: "Subscription Line Id",
                        // sort: true
                        dataField: "description",
                        text: "Description",
                        sort: true
                    }, {
                        dataField: "period",
                        text: "Period",
                        sort: true
                    }, {
                        dataField: "rounded_price_per_piece",
                        text: "Unit Price",
                        sort: true
                    },
                    // {
                    //     dataField: "subscription_line.line_type.line_type",
                    //     text: "Subscription Line Type",
                    //     sort: true
                    // },
                    // {
                    //     dataField: "plan_line_id",
                    //     text: "Plan Line Id",
                    //     sort: true
                    // },
                    // {
                    //     dataField: "plan_line.line_type.line_type",
                    //     text: "Plan Line Type",
                    //     sort: true
                    // },
                    //     , {
                    //     dataField: "description",
                    //     text: "Subscription",
                    //     sort: true
                    // }, 
                    {
                        dataField: "quantity",
                        text: "Quantity",
                        sort: true
                    }, {
                        dataField: "rounded_price",
                        text: "Price",
                        sort: true
                    }, {
                        dataField: "rounded_price_vat",
                        text: "VAT:",
                        sort: true
                    }, {
                        dataField: "rounded_price_total",
                        text: "Total",
                        sort: true
                    }
                        // ,
                        // {
                        //   dataField: "actions",
                        //   text: "Actions",
                        //   formatter: this.actionsFormatter
                        // }
                    ]}
                    search
                >
                    {
                        props => (
                            <React.Fragment>
                                {/* <CardHeader>
                                    <Row>
                                        <Col xs="12" md="6" lg="3">
                                            <SearchBar {...props.searchProps} />
                                        </Col>
                                    </Row>
                                </CardHeader> */}
                                <CardBody>
                                    <BootstrapTable striped
                                        {...props.baseProps}
                                        bootstrap4
                                        bordered={false}
                                        pagination={paginationFactory({
                                            sizePerPage: 10,
                                            sizePerPageList: [10, 25, 50]
                                        })}
                                    />
                                </CardBody>
                            </React.Fragment>
                        )
                    }
                </ToolkitProvider>
            );
        }

        return (
            <Container fluid className="p-0">
                <Card>
                    {!this.state.loading ?
                        <React.Fragment>
                            <InvoiceCardHeader />
                            {this.state.sales_invoice ? <BasicDetails sales_invoice={this.state.sales_invoice} /> : null}
                        
                            {this.state.sales_invoice ? <SalesInvoiceLines /> : null}
                        </React.Fragment>
                        : <Loader />
                    }

                    {this.state.isOpen ?
                        <SalesInvoiceForm
                            show={this.state.isOpen}
                            hide={this.toggleModal}
                            id={this.props.match.params.id}
                            update={this.updateComponent}
                            details={this.state.sales_invoice}
                        /> : null
                    }
                </Card>

            </Container>
        );
    }
}

export default InvoiceDetails;