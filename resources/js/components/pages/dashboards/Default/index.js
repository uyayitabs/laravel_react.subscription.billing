import React from "react";
import { Container, Row, Col } from "reactstrap";

import SubscriptionsCountPanel from "./SubscriptionsCountPanel";
import TenantsCountPanel from "./TenantsCountPanel";
import PersonsCountPanel from "./PersonsCountPanel";
import InvoicesCountPanel from "./InvoicesCountPanel";
import ProductsCountPanel from "./ProductsCountPanel";

import LatestSubscriptions from "./LatestSubscriptions";

const Default = () => (
    <Container fluid className="p-0">
        <Row>
            <Col>
                <SubscriptionsCountPanel />
            </Col>
            <Col>
                <TenantsCountPanel />
            </Col>
            <Col>
                <PersonsCountPanel />
            </Col>
            <Col>
                <InvoicesCountPanel />
            </Col>
            <Col>
                <ProductsCountPanel />
            </Col>
        </Row>
        <Row>
            <Col lg="12" xl="12" className="d-flex">
                <LatestSubscriptions />
            </Col>
        </Row>
    </Container>
);

export default Default;
